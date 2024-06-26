<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class RegistrationController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager,
                                private readonly UserPasswordHasherInterface $userPasswordHasher,
                                private readonly RequestStack $requestStack)
    {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/registration', name: 'registration.show')]
    public function index(Request $request): Response
    {
        $user = new User();
        $registrationForm = $this->createForm(RegistrationType::class, $user);

        $registrationForm->handleRequest($request);

        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            $user->setApiActivated(false)->setRegistrationDate(new \DateTime())->setRoles(['ROLE_USER']);

            $cart = new Cart();
            $cart->setCreatedAt(new \DateTimeImmutable());
            $user->setCart($cart);

            $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPassword()));

            $this->entityManager->persist($cart);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Connecte automatiquement l'utilisateur après son inscription
            $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
            $this->container->get('security.token_storage')->setToken($token);
            $this->requestStack->getSession()->set('_security_main', serialize($token));

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/index.html.twig', [
            'registrationForm' => $registrationForm->createView(),
        ]);
    }
}
