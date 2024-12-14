<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/supply-chain', name: 'supply_chain')]
class SupplyChainController extends AbstractController
{
    #[Route('/', name: 'supply_chain-index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('supply_chain/index.html.twig', [
            'controller_name' => 'SupplyChainController',
        ]);
    }
}
