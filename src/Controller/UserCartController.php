<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserCartController extends AbstractController
{
    #[Route('/panier', name: 'cart.show')]
    public function index(): Response
    {
        return $this->render('user_cart/index.html.twig', [
            'controller_name' => 'UserCartController',
        ]);
    }
}
