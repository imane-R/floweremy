<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Ajouter des données de test si nécessaire
        // Exemple de création d'un utilisateur
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password');
        $user->setNom('Doe');
        $user->setPrenom('John');
        // Remplissez les autres champs selon votre entité User

        $manager->persist($user);
        $manager->flush();
    }
}
