<?php
// src/Service/EventCapacityCalculator.php

namespace App\Service;

use App\Entity\Event;

class EventCapacityCalculator
{
    public function calculateRemainingPlaces(Event $event): int
    {
// Calcul du nombre de places restantes
        $currentParticipants = $event->getParticipant()->count();
        $capacity = $event->getCapacity();
        $remainingPlaces = $capacity - $currentParticipants;

        return $remainingPlaces;
    }
}
