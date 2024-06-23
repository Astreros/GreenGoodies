<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    public function __construct(private readonly ArticleRepository $articleRepository)
    {
    }

    #[Route('/article/{id}', name: 'product.show', defaults: ['id' => -1])]
    public function index(int $id): Response
    {
        $articles = $this->articleRepository->findBy([], ['name' => 'ASC']);

        if ($id === -1) {
            return $this->render('product/index.html.twig', [
                'articles' => $articles,
            ]);
        }

        $article = $this->articleRepository->find($id);

        if ($article === null) {
            return $this->redirectToRoute('product.show');
        }

        return $this->render('product/details.html.twig', [
            'article' => $article,
        ]);
    }
}
