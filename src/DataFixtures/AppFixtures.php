<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('user@greengoodies.com')->setFirstName('john')->setLastName('Doe');
        $user->setApiActivated(false)->setCguAccepted(true);
        $user->setRoles(['ROLE_USER']);
        $user->setRegistrationDate(new \DateTime());
        $user->setPassword($this->userPasswordHasher->hashPassword($user,'password'));
        $manager->persist($user);

        $manager->flush();
    }
}
