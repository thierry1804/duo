<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
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
}
