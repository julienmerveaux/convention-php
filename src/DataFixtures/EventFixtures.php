<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EventFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $users = $manager->getRepository(User::class)->findAll();

        if (empty($users)) {
            throw new \Exception('Aucun utilisateur trouvé dans la base de données.');
        }

        $userCount = count($users);

        for ($i = 0; $i < 15; $i++) {
            $event = new Event();
            $event->setTitle('title' . $i);
            $event->setDescription('description' . $i);
            $event->setDatetime(new \DateTime());
            $event->setCapacity($i);
            $event->setIsPublic($i % 2 == 0);

            // Assigner un utilisateur à chaque événement
            $user = $users[$i % $userCount];
            $event->setCreator($user);

            $manager->persist($event);
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 2; // Priorité 2
    }
}

