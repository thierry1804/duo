<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticlesType;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Imagine\Gd\Imagine;
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

                $imagine = new Imagine();
                $image = $imagine->open($this->getParameter('images_directory').'/'.$fichier);
                $imageSize = $this->getSizeOfAnImage($image);
                $watermarkPath = $this->getParameter('watermark_directory');
                $watermark = $imagine->open($watermarkPath);
                $watermark->resize(new Box($imageSize->getWidth() / 2, $imageSize->getWidth() / 2));
                $watermarkPosition = new Point(0, $imageSize->getHeight() - ($imageSize->getWidth() / 2));
                $image->paste($watermark, $watermarkPosition);
                $image->save($this->getParameter('images_directory').'/'.$fichier);

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

    #[Route('/{id}', name: 'app_article_show', methods: ['GET'])]
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
    #[Route('/{id}', name: 'app_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
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
