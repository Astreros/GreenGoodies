<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{

    public function __construct(private readonly ArticleRepository $articleRepository)
    {
    }

    #[Route('/', name: 'home.show')]
    public function index(): Response
    {
        $lastArticles = $this->articleRepository->findBy([], ['createdAt' => 'DESC'], 9);

        return $this->render('home/index.html.twig', [
            'lastArticles' => $lastArticles,
        ]);
    }
}
