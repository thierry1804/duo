<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('/search', name: 'app_search')]
    public function index(Request $request, CategoryRepository $categoryRepository,
                          ArticleRepository $articleRepository): Response
    {
        $q = $request->request->get('q');

        $categoriesFound = $categoryRepository->findByCategory($q);

        //Find all categories related to the search
        $categories = $categoryRepository->getCategories();

        //Find all articles related to the search
        $articlesFound = $articleRepository->findByArticle($q);

        return $this->render('search/index.html.twig', [
            'categories' => $categories,
            'categoriesFound' => $categoriesFound,
            'articlesFound' => $articlesFound,
        ]);
    }
}
