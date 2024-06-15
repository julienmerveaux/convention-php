<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements OrderedFixtureInterface
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setFirstname('firstname'.$i);
            $user->setLastname('lastname'.$i);
            $user->setEmail('email'.$i.'@gmail.com');

            // Hasher le mot de passe 'password' pour chaque utilisateur
            $hashedPassword = $this->passwordHasher->hashPassword($user, 'password'.$i);
            $user->setPassword($hashedPassword);

            // Ajouter un rôle pour chaque utilisateur (ici ROLE_USER)
            $user->setRoles(['ROLE_USER']);

            $manager->persist($user);
        }

        $manager->flush();
    }
    public function getOrder(): int
    {
        return 1;
    }
}
