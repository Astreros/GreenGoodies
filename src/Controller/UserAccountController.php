<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserAccountController extends AbstractController
{
    #[Route('/account', name: 'account.show')]
    public function index(): Response
    {
        return $this->render('user_account/index.html.twig', [
            'controller_name' => 'UserAccountController',
        ]);
    }
}
