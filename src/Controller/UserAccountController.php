<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserAccountController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager,
                                private readonly UserRepository $userRepository,
                                private readonly OrderRepository $orderRepository)
    {
    }

    #[Route('/account', name: 'account.show')]
    #[IsGranted("ROLE_USER")]
    public function index(): Response
    {
        $user = $this->getUser();

        $orders = $this->orderRepository->findBy(['user' => $user], ['createdAt' => 'ASC']);

        if (empty($orders)) {
            $orders = false;
        }

        return $this->render('user_account/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/account/api/enabled', name: 'api.enabled')]
    #[IsGranted("ROLE_USER")]
    public function enabledApiAccess(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $user->setApiActivated(true);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('account.show');
    }

    #[Route('/account/api/disabled', name: 'api.disabled')]
    #[IsGranted("ROLE_USER")]
    public function disabledApiAccess(): Response
    {
        $user = $this->userRepository->find($this->getUser()->getId());

        if (!$user) {
            return $this->redirectToRoute('account.show');
        }

        $user->setApiActivated(false);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('account.show');
    }

    #[Route('/account/delete', name: 'account.delete')]
    #[IsGranted("ROLE_USER")]
    public function deleteAccount(Request $request): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createNotFoundException('User not found');
        }

        $session = new Session();
        $session->invalidate();

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('home.show');
    }
}
