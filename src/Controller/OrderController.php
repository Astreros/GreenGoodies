<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    #[Route('/order/create', name: 'order.create')]
    public function index(): Response
    {



        return $this->render('user_account/index.html.twig', [
            'controller_name' => 'OrderController',
        ]);
    }
}
