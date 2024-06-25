<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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

        $orders = $this->orderRepository->findBy(['user' => $user]);

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
}
