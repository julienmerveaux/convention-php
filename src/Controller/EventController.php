<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class EventController extends AbstractController
{

    #[Route('/events', name: 'event_list')]
    public function index(EventRepository $repository): JsonResponse
    {
        return $this->json([
            "events" => $repository->findAll()
        ]);
    }

//    #[Route('/updateEvent', name: 'app_event')]
//    public function index(): JsonResponse
//    {
//        return $this->json([
//            'message' => 'Welcome to your new controller!',
//            'path' => 'src/Controller/EventController.php',
//        ]);
//    }

    #[Route('/getEventWithFilter', name: 'app_event')]
    public function getEventWithFilter(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/EventController.php',
        ]);
    }

    #[Route('/createEvent', name: 'app_event')]
    public function createEvent(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/EventController.php',
        ]);
    }
}
