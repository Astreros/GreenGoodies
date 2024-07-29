<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class ApiArticleController extends AbstractController
{
    public function __construct(private readonly ArticleRepository $articleRepository,
                                private readonly SerializerInterface $serializer)
    {
    }


    #[Route('/api/products', name: 'products', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne la liste des produits',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Article::class))
        )
    )]
    #[OA\Tag(name: 'Produit')]
    public function getAllProducts(): JsonResponse
    {
        $products = $this->articleRepository->findAll();

        $jsonProducts = $this->serializer->serialize($products, 'json');

        return new JsonResponse($jsonProducts, Response::HTTP_OK, [], true);
    }
}
