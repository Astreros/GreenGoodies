<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Random\RandomException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    /**
     * @throws RandomException
     */
    public function load(ObjectManager $manager): void
    {
        // Création d'un utilisateur ROLE_USER
        $user = new User();
        $user->setEmail('user@greengoodies.com')->setFirstName('john')->setLastName('Doe');
        $user->setApiActivated(false)->setCguAccepted(true);
        $user->setRoles(['ROLE_USER']);
        $user->setRegistrationDate(new \DateTime());
        $user->setPassword($this->userPasswordHasher->hashPassword($user,'password'));
        $manager->persist($user);


        // Création de 9 articles
        $minArticlePrice = 5;
        $maxArticlePrice = 50;

        $minArticleStock = 5;
        $maxArticleStock = 30;

        $articleName = [
            "Kit d'hygiène recyclable",
            "Shot Tropical",
            "Gourde en bois",
            "Disques Démaquillants x3",
            "Bougie Lavande & Patchouli",
            "Brosse à dent",
            "Kit couvert en bois",
            "Nécessaire, déodorant Bio",
            "Savon Bio"
        ];

        $articleShortDescription = [
            "Pour une salle de bain éco-friendly",
            "Fruits frais, pressés à froid",
            "50cl, bois d’olivier",
            "Solution efficace pour vous démaquiller en douceur ",
            "Cire naturelle",
            "Bois de hêtre rouge issu de forêts gérées durablement",
            "Revêtement Bio en olivier & sac de transport",
            "50ml déodorant à l’eucalyptus",
            "Thé, Orange & Girofle"
        ];

        $articlesImagePath = [
            "articles/0f07c28090abf9ac0d263bf4473ba9a6.jpg",
            "articles/14b95ab56656af06d7a69ab2d9ee44d0.jpg",
            "articles/5c542819963e653209f118071a79567b.jpg",
            "articles/83102a01875727a5366e6a6fa9a75445.jpg",
            "articles/c4700f712d7bef2fade2b494d4d2cd98.jpg",
            "articles/edebd52c007e82d992ba79ed0df88597.jpg",
            "articles/9499862906fd402eb6ed9de766d7b289.jpg",
            "articles/15cd7d442e6e9686d8678f0c1236a01f.jpg",
            "articles/fa1216c1cf674ce8c25148d240677fed.jpg",
        ];

        for ($i = 0; $i < 9; $i++) {
            $article = new Article();

            $article->setName($articleName[$i]);
            $article->setShortDescription($articleShortDescription[$i]);
            $article->setLongDescription("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris dictum vestibulum nulla non varius. Phasellus in ornare turpis, in rutrum urna. Vestibulum varius tempus nisi nec facilisis. In suscipit eu turpis ut venenatis. Curabitur blandit pellentesque massa in maximus. Donec molestie, ipsum a pretium bibendum, augue dolor eleifend felis, sit amet bibendum enim mi eu dui. Sed placerat commodo purus, non commodo massa hendrerit ac. Aliquam ullamcorper ante odio, at molestie massa gravida non. Nam et velit vel orci congue ultricies. Vivamus ornare metus vitae ante sagittis eleifend. Suspendisse consectetur enim a placerat convallis. Aliquam quis pretium eros, ut suscipit elit. Donec vel eleifend arcu. Etiam varius hendrerit neque sed lacinia.
                                                        Maecenas eget porta massa. Ut blandit lectus sit amet varius mollis. Sed elementum quam dolor. Morbi scelerisque, nisl non blandit sodales, odio lacus tincidunt augue, eu feugiat massa massa non tortor. Sed ac fermentum mi, vitae volutpat dui. Nunc ut nibh a velit cursus varius. Cras sit amet mauris egestas, suscipit elit id, vehicula felis. Donec malesuada nisl non ipsum fringilla cursus. Praesent hendrerit tortor a lectus consequat, vitae consectetur libero pharetra. ");
            $article->setImagePath($articlesImagePath[$i]);
            $article->setPrice(random_int($minArticlePrice*100, $maxArticlePrice*100) / 100);
            $article->setStock(random_int($minArticleStock, $maxArticleStock));
            $article->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($article);
        }

        $manager->flush();
    }
}
