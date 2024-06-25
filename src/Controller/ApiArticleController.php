<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiArticleController extends AbstractController
{
    public function __construct(private readonly ArticleRepository $articleRepository,
                                private readonly SerializerInterface $serializer)
    {
    }


    #[Route('/api/products', name: 'products', methods: ['GET'])]
    public function getAllProducts(): JsonResponse
    {
        $products = $this->articleRepository->findAll();

        $jsonProducts = $this->serializer->serialize($products, 'json');

        return new JsonResponse($jsonProducts, Response::HTTP_OK, [], true);
    }
}
