<?php

namespace App\Controller;

ini_set('max_file_uploads', '22');

use App\Entity\Article;
use App\Entity\Category;
use App\Form\ArticlesType;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Imagine\Gd\Imagine;
use Imagine\Gd;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/article')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_article_index', methods: ['GET'])]
    public function index(Request $request, ArticleRepository $articleRepository): Response
    {
        $offset = $request->query->getInt('offset', 0);
        $paginator = $articleRepository->getArticlesPaginator($offset);

        return $this->render('article/index.html.twig', [
            'articles' => $paginator,
            'previous' => $offset - ArticleRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + ArticleRepository::PAGINATOR_PER_PAGE),
            'uploadsPath' => $this->getParameter('images_directory'),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/add', name: 'app_article_add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticlesType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            $category = $form->get('category')->getData();

            foreach ($images as $image) {
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                $this->addWatermarkOnImage($fichier);

                $article = new Article();
                $article->setCategory($category);
                $article->setImage($fichier);
                $entityManager->persist($article);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/add.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_article_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Article $article, ArticleRepository $articleRepository): Response
    {
        $articleBefore = $articleRepository->getArticleBeforeId($article->getId());
        $articleAfter = $articleRepository->getArticleAfterId($article->getId());
        return $this->render('article/show.html.twig', [
            'article' => $article,
            'articleBefore' => $articleBefore,
            'articleAfter' => $articleAfter,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager,
                         ArticleRepository $articleRepository): Response
    {
        $articleBefore = $articleRepository->getArticleBeforeId($article->getId());
        $articleAfter = $articleRepository->getArticleAfterId($article->getId());
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if ($image) {
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                $article->setImage($fichier);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
            'articleBefore' => $articleBefore,
            'articleAfter' => $articleAfter,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_article_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/sample/{id}', name: 'app_article_sample', methods: ['GET'])]
    public function getSampleArticlesByCategory(Category $category, ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->getArticlesByCategory($category);
        return $this->render('article/_sample.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/upload', name: 'app_article_image_upload', methods: ['POST'])]
    public function upload(Request $request, EntityManagerInterface $entityManager,
                           CategoryRepository $categoryRepository): Response
    {
        $images = $request->files->get('images');
        $category = $categoryRepository->find($request->request->get('category'));

        $extension = $images->guessExtension();
        $filename = md5(uniqid());
        $fichier = $filename.'.'.$extension;
        $images->move(
            $this->getParameter('images_directory'),
            $fichier
        );

        //convert the image to webp
        $newFichier = $filename . '.webp';
        switch ($extension) {
            case 'jpeg':
            case 'jpg':
                $image = imagecreatefromjpeg($this->getParameter('images_directory').'/'.$fichier);
                break;
            case 'png':
                $image = imagecreatefrompng($this->getParameter('images_directory').'/'.$fichier);
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
                break;
            default:
                return new Response('Invalid image type', Response::HTTP_BAD_REQUEST);
        }
        imagewebp($image, $this->getParameter('images_directory').'/'.$newFichier, 100);

        $article = new Article();
        $article->setCategory($category);
        $article->setImage($fichier);
        $entityManager->persist($article);

        $entityManager->flush();

        return new Response(
            'Images uploaded successfully. ' . $this->addWatermarkOnImage($fichier),
            Response::HTTP_CREATED
        );
    }

    /**
     * @param string $fichier
     * @return string
     */
    private function addWatermarkOnImage(string $fichier): string
    {
        try {
            $imagine = new Imagine();
            $image = $imagine->open($this->getParameter('images_directory').'/'.$fichier);
            $imageSize = $this->getSizeOfAnImage($image);
            $watermarkPath = $this->getParameter('watermark_directory');
            $watermark = $imagine->open($watermarkPath);
            $watermark->resize(new Box($imageSize->getWidth() / 2, $imageSize->getWidth() / 2));
            $watermarkPosition = new Point(0, $imageSize->getHeight() - ($imageSize->getWidth() / 2));
            $image->paste($watermark, $watermarkPosition);
            $image->save($this->getParameter('images_directory').'/'.$fichier);
            return 'Watermark added successfully';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param ImageInterface $image
     * @return Box
     */
    private function getSizeOfAnImage(ImageInterface $image): Box
    {
        $size = $image->getSize();

        return new Box($size->getWidth(), $size->getHeight());
    }
}
