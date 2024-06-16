<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventFormType;
use App\Repository\EventRepository;
use App\Service\MailJetService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
        foreach ($result['data'] as $event) {
            $participantsCount = count($event->getParticipant());
            $event->participantsCount = $participantsCount;
            $event->setremainingPlaces = $event->getCapacity() - $participantsCount;
        }
        return $this->render('Accueil.html.twig', [
            'events' => $result['data'],
            'total' => $result['total'],
            'page' => $result['page'],
            'limit' => $result['limit'],
        ]);
    }

    #[Route('/createEvent', name: 'createEvent')]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event, [
            'submit_label' => 'Créer', // Utiliser 'Créer' comme label du bouton
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if ($user) {
                $event->setCreator($user);
                $manager->persist($event);
                $manager->flush();

                // Ajouter l'événement à la liste des événements créés par l'utilisateur
                $user->addListEventCreated($event);
                $manager->persist($user);
                $manager->flush();
            }

            return $this->redirectToRoute('accueil');
        }

        return $this->render('form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/removeUser/{eventId}', name: 'removeUser')]
    #[IsGranted('ROLE_USER')]
    public function removeUser(int $eventId, EntityManagerInterface $entityManager, EventRepository $eventRepository, MailJetService $mailJetService): Response
    {
        // Récupérer l'événement par son ID
        $event = $eventRepository->find($eventId);

        if (!$event) {
            throw $this->createNotFoundException('L\'événement avec l\'ID ' . $eventId . ' n\'existe pas.');
        }

        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        if ($event->getParticipant()->contains($user)) {
            $event->removeParticipant($user);
            $event->setRemainingPlaces($event->getRemainingPlaces() + 1);
            $entityManager->persist($event);
            $entityManager->flush();

            // Envoyer un email de confirmation d'annulation
            $mailJetService->sendCancellationConfirmation($user->getEmail());

            $this->addFlash('success', 'Vous avez été désinscrit de l\'événement.');
        } else {
            $this->addFlash('error', 'Vous n\'étiez pas inscrit à cet événement.');
        }

        return $this->redirectToRoute('accueil'); // Assurez-vous d'adapter cette redirection à votre structure de routes
    }
    #[Route('/event/edit/{eventId}', name: 'editEvent')]
    #[IsGranted('ROLE_USER')]
    public function editEvent(int $eventId, Request $request, EntityManagerInterface $manager, EventRepository $repository): Response
    {
        $event = $repository->find($eventId);

        if (!$event) {
            throw $this->createNotFoundException('No event found for id ' . $eventId);
        }

        $form = $this->createForm(EventFormType::class, $event, [
            'submit_label' => 'Enregistrer', // Utiliser 'Enregistrer' comme label du bouton
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
            return $this->redirectToRoute('profil_user');
        }

        return $this->render('form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
