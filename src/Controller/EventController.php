<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventFormType;
use App\Repository\EventRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class EventController extends AbstractController
{

    #[Route('/events', name: 'event_list')]
    public function index(EventRepository $repository): \Symfony\Component\HttpFoundation\Response
    {
        $Event = $repository->findAll();

        return $this->render('Acceuil.html.twig', [
            'event' => $Event,
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

    #[Route('/createEvent', name: 'event_new')]
    public function new(Request $request, ManagerRegistry $managerRegistry): \Symfony\Component\HttpFoundation\Response
    {
        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $managerRegistry->getManager();
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('events');
        }

        return $this->render('new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
