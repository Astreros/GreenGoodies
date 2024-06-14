<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/product/{id}', name: 'product.show')]
    public function index(int $id): Response
    {
        return $this->render('product/index.html.twig', [
            'productID' => $id,
        ]);
    }
}
