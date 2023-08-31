<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Faker\Factory as FakerFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    private $faker; // Permet de générer des string aléatoire (email, user, etc)
    private $hasher; // Permet de hasher les MDP
    private $manager; // Permet de stocker des données dans une BDD

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->faker = FakerFactory::create('fr_FR');
        $this->hasher = $hasher;
    }

    public static function getGroups(): array
    {
        return ['prod', 'test'];
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->loadAdmins();
    }

    public function loadAdmins() : void 
    {
        // données statiques
        $datas = [
            [
                'email' => 'admin@example.com',
                'password' => '123',
                'roles' => ['ROLE_ADMIN']
            ],
        ];

        foreach ($datas as $data) {
        $user = new User();
        $user->setEmail($data['email']);
        $password = $this->hasher->hashPassword($user, $data['password']);
        $user->setPassword($password);
        $user->setRoles($data['roles']);

        /*
        le code précédent equivaut en SQL à :

        INSERT INTO user
        (email, password, roles)
        VALUES
        ('admin@example.com', '123', '[\'ROLE_ADMIN\']')
        */

        $this->manager->persist($user); // génere le code SQL pour stocker le user dans la BDD
        }

        $this->manager->flush(); // éxécute le code SQL de persist() pour poussé le code en BDD
    }

}
