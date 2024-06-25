<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\User;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserCartController extends AbstractController
{
    public function __construct(private readonly ArticleRepository $articleRepository, private readonly EntityManagerInterface $entityManagerInterface)
    {
    }

    #[Route('/panier', name: 'cart.show')]
    #[IsGranted("ROLE_USER")]
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            $this->redirectToRoute('app_login');
        }

        $cart = $user->getCart();

        $totalPrice = 0;

        foreach ($cart->getCartItem() as $cartItem) {
            $totalPrice += $cartItem->getArticle()->getPrice() * $cartItem->getQuantity();
        }

        return $this->render('user_cart/index.html.twig', [
            'cart' => $cart,
            'totalPrice' => $totalPrice,
        ]);
    }

    #[Route('/panier/add/{id}', name: 'cart.add')]
    #[IsGranted("ROLE_USER")]
    public function addItemToCart(Request $request, int $id): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            $this->redirectToRoute('app_login');
        }

        $cart = $user->getCart();

        if (!$cart) {
            $cart = new Cart();
            $cart->setCreatedAt(new \DateTimeImmutable());
            $user->setCart($cart);
        }

        $article = $this->articleRepository->find($id);

        $userQuantity = $request->request->get('quantity');
        $quantityChecked = $this->checkQuantity($id, $userQuantity);

        $cartItem = new CartItem();
        $cartItem->setArticle($article);
        $cartItem->setQuantity($quantityChecked);
        $cartItem->setCart($cart);

        $cart->addCartItem($cartItem);

        $this->entityManagerInterface->persist($cartItem);
        $this->entityManagerInterface->persist($cart);
        $this->entityManagerInterface->persist($user);

        $this->entityManagerInterface->flush();

        return $this->redirectToRoute('cart.show');
    }

    #[Route('/panier/update/{id}', name: 'cart.update')]
    #[IsGranted("ROLE_USER")]
    public function updateItemToCart(Request $request, int $id): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createNotFoundException('User not found');
        }

        $cart = $user->getCart();
        if (!$cart) {
            throw $this->createNotFoundException('Cart not found');
        }

        $article = $this->articleRepository->find($id);
        if (!$article instanceof Article) {
            throw $this->createNotFoundException('Article not found');
        }

        $userQuantity = $request->request->get('quantity');

        $quantityChecked = $this->checkQuantity($id, $userQuantity);

        $cartItemToUpdate = null;

        foreach ($cart->getCartItem() as $cartItem) {
            $itemArticle = $cartItem->getArticle();
            // Item du panier non valide, supprime l'élément
            if($itemArticle === null) {
                $cart->removeCartItem($cartItem);
                $this->entityManagerInterface->remove($cartItem);
                $this->entityManagerInterface->flush();
                continue;
            }

            if ($itemArticle->getId() === $article->getId()) {
                $cartItemToUpdate = $cartItem;
                break;
            }
        }

        if ($cartItemToUpdate !== null) {
            if ($quantityChecked === 0) {
                $cart->removeCartItem($cartItemToUpdate);
                $this->entityManagerInterface->remove($cartItemToUpdate);
            } else {
                $cartItemToUpdate->setQuantity($quantityChecked);
                $this->entityManagerInterface->persist($cartItemToUpdate);
            }
            $this->entityManagerInterface->flush();
        }

        return $this->redirectToRoute('cart.show');
    }

    #[Route('/panier/empty', name: 'cart.empty')]
    #[IsGranted("ROLE_USER")]
    public function emptyCart(): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createNotFoundException('User not found');
        }

        $cart = $user->getCart();
        if (!$cart) {
            throw $this->createNotFoundException('Cart not found');
        }

        foreach ($cart->getCartItem() as $cartItem) {
            $cart->removeCartItem($cartItem);
            $this->entityManagerInterface->remove($cartItem);
        }

        $this->entityManagerInterface->flush();

        return $this->redirectToRoute('cart.show');
    }

    private function checkQuantity(int $id, int $quantityUser): int
    {
        $article = $this->articleRepository->find($id);

        if (!$article instanceof Article) {
            throw $this->createNotFoundException('Article not found');
        }

        $articleQuantity = $article->getStock();

        if ($quantityUser > $articleQuantity) {
            return $articleQuantity;
        }

        if ($quantityUser < 1) {
            return 0;
        }

        return $quantityUser;
    }
}
