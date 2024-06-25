<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManagerInterface)
    {
    }

    #[Route('/order/create', name: 'order.create')]
    public function index(): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            $this->redirectToRoute('app_login');
        }

        $cart = $user->getCart();
        if (!$cart instanceof Cart) {
            throw $this->createNotFoundException('Cart not found');
        }

        $now = new \DateTime();
        $nbOrder = $now->getTimestamp() . $user->getId();

        $order = new Order();
        $order->setUser($user);
        $order->setNbOrder($nbOrder);
        $order->setStatus("En cours de traitement");
        $order->setCreatedAt(new \DateTimeImmutable());

        $this->entityManagerInterface->persist($order);
        $this->entityManagerInterface->flush();

        foreach ($cart->getCartItem() as $cartItem) {
            $article = $cartItem->getArticle();
            if (!$article instanceof Article) {
                throw $this->createNotFoundException('Article not found');
            }

            $orderItem = new OrderItem();
            $orderItem->setOrders($order);
            $orderItem->setArticle($article);
            $orderItem->setQuantity($cartItem->getQuantity());
            $orderItem->setUnitPrice($article->getPrice());

            $this->entityManagerInterface->persist($orderItem);

            $article->setStock($article->getStock() - $cartItem->getQuantity());
            $this->entityManagerInterface->persist($article);
        }

        foreach ($cart->getCartItem() as $cartItem) {
            $cart->removeCartItem($cartItem);
            $this->entityManagerInterface->remove($cartItem);
        }

        $this->entityManagerInterface->flush();

        return $this->redirectToRoute('account.show');
    }
}
