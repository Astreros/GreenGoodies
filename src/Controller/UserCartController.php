<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserCartController extends AbstractController
{
    #[Route('/panier', name: 'cart.show')]
    #[IsGranted("ROLE_USER")]
    public function index(): Response
    {
        return $this->render('user_cart/index.html.twig', [
            'controller_name' => 'UserCartController',
        ]);
    }
}
