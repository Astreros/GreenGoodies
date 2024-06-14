<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/product/{id}', name: 'product.show', defaults: ['id' => -1])]
    public function index(int $id): Response
    {
        if ($id === -1) {
            return $this->redirectToRoute('home.show');
        }

        return $this->render('product/index.html.twig', [
            'productID' => $id,
        ]);
    }
}
