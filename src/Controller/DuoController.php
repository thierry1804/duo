<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DuoController extends AbstractController
{
    #[Route('/', name: 'app_duo')]
    public function index(): Response
    {
        return $this->render('duo/index.html.twig', [
            'controller_name' => 'DuoController',
        ]);
    }
}
