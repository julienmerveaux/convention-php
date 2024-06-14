<?php

namespace App\DataFixtures;

use App\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EventFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 5; $i++) {
            $event = new Event();
            $event->setTitle('title' . $i);
            $event->setDescription('description' . $i);
            $event->setDatetime(new \DateTime());
            $event->setCapacity($i);
            $event->setPublic($i%2 == 0);
            $manager->persist($event);
        }
        $manager->flush();
    }
}
