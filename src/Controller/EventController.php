<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventFormType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class EventController extends AbstractController
{


    #[Route('/', name: 'accueil')]
    public function index(Request $request, EventRepository $repository): Response
    {
        $title = $request->query->get('title', '');
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 5);
        $is_connected = $this->getUser() !== null;

        $result = $repository->findByTitleLike($is_connected, $page, $limit, $title);

        return $this->render('Accueil.html.twig', [
            'events' => $result['data'],
            'total' => $result['total'],
            'page' => $result['page'],
            'limit' => $result['limit'],
        ]);
    }
//    #[Route('/addUser/{eventId}', name: 'addUser')]
//    #[IsGranted('ROLE_USER')]
//    public function addUser($eventId, EntityManagerInterface $entityManager, EventRepository $eventRepository): Response    {
//        // Récupérer l'événement par son ID
//        $event = $eventRepository->find($eventId);
//
//        if (!$event) {
//            throw $this->createNotFoundException('L\'événement avec l\'ID '.$eventId.' n\'existe pas.');
//        }
//
//        // Récupérer l'utilisateur connecté
//        $user = $this->getUser();
//
//        // Ajouter l'utilisateur à l'événement
//        $event->addParticipant($user);
//
//        // Sauvegarder les changements dans la base de données
//        $entityManager->flush();
//
//        // Retourner une réponse JSON pour indiquer le succès de l'inscription
//        return $this->json(['success' => true]);
//    }
//

    #[Route('/createEvent', name: 'createEvent')]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $managerRegistry->getManager();
            $user = $this->getUser();

            if ($user) {
                // Persist the event first to get its ID
                $entityManager->persist($event);
                $entityManager->flush();  // Now the event ID is generated

                // Add the event to the user's events
                $user->addParticpantInEvent($event);
                $entityManager->persist($user);
                $entityManager->flush();
            }

            return $this->redirectToRoute('accueil');
        }

        return $this->render('new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
