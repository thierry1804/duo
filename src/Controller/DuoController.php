<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Doctrine\DBAL\Exception;
use Stichoza\GoogleTranslate\Exceptions\LargeTextException;
use Stichoza\GoogleTranslate\Exceptions\RateLimitException;
use Stichoza\GoogleTranslate\Exceptions\TranslationRequestException;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DuoController extends AbstractController
{
    #[Route('/', name: 'app_duo')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->getCategories();
        return $this->render('duo/index.html.twig', [
            'controller_name' => 'DuoController',
            'categories' => $categories,
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/home', name: 'app_home')]
    public function home(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->getCategories();
        return $this->render('duo/home.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/v2', name: 'app_v2')]
    public function newHom(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->getCategories(0);
        $categoriesFooter = $categoryRepository->getCategories(5);
        return $this->render('duo/v2.html.twig', [
            'categories' => $categories,
            'categoriesFooter' => $categoriesFooter,
        ]);
    }

    /**
     * @throws LargeTextException
     * @throws RateLimitException
     * @throws TranslationRequestException
     */
    #[Route('/translate/{q}', name: 'app_translate')]
    public function translate(string $q): Response
    {
        $tr = new GoogleTranslate('fr', 'en');
        $q = $tr->translate($q);
        return new Response($q);
    }
}
