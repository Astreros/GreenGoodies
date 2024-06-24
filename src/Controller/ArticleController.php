<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\User;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ArticleController extends AbstractController
{
    public function __construct(private readonly ArticleRepository $articleRepository)
    {
    }

    #[Route('/article/{id}', name: 'product.show', defaults: ['id' => -1])]
    public function index(int $id): Response
    {
        if ($id === -1) {
            $articles = $this->articleRepository->findBy([], ['name' => 'ASC']);

            return $this->render('product/index.html.twig', [
                'articles' => $articles,
            ]);
        }

        $article = $this->articleRepository->find($id);
        if ($article === null) {
            return $this->redirectToRoute('product.show');
        }

        $articleInCart = false;
        $articleQuantity = 1;

        if ($this->isGranted('ROLE_USER')) {
            $articleQuantity = $this->articleIsInCart($id);

            if ($articleQuantity) {
                $articleInCart = true;
            }
        }

        return $this->render('product/details.html.twig', [
            'article' => $article,
            'articleInCart' => $articleInCart,
            'articleQuantity' => $articleQuantity,
        ]);
    }

    #[IsGranted("ROLE_USER")]
    public function articleIsInCart(int $articleId): int|false
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            $this->redirectToRoute('app_login');
        }

        $cart = $user->getCart();

        if (!$cart instanceof Cart) {
            return false;
        }

        $cartItems = $cart->getCartItem();

        if ($cartItems->isEmpty()) {
            return false;
        }
        
        $articleInCart = false;
        $articleQuantity = 0;

        foreach ($cartItems as $cartItem) {
            $article = $cartItem->getArticle();

            if ($article !== null && $article->getId() === $articleId) {
                $articleInCart = true;
                $articleQuantity = $cartItem->getQuantity();
            }
        }

        return $articleInCart ? $articleQuantity : false;
    }
}
