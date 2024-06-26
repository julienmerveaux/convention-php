<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Form\ConfirmPasswordType;
use App\Form\UpdatePasswordFormType;
use App\Form\UserFormType;
use App\Repository\EventRepository;
use App\Security\Voter\UserVoter;
use App\Service\MailJetService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Service\EventCapacityCalculator;

class UserController extends AbstractController
{
    private EventCapacityCalculator $eventCapacityCalculator;

    public function __construct(EventCapacityCalculator $eventCapacityCalculator)
    {
        $this->eventCapacityCalculator = $eventCapacityCalculator;
    }
    #[Route('/profil_user', name: 'profil_user')]
    public function index(EventRepository $eventRepository): \Symfony\Component\HttpFoundation\Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        $ListEventCreated = $user->getListEventCreated();
        $ListEventsInscribed = $user->getEvents(); // Assurez-vous que cette méthode existe et fonctionne
        if (!$user instanceof UserInterface) {
            return $this->redirectToRoute('login');
        }
        $this->denyAccessUnlessGranted(UserVoter::VIEW, $user);
        // Rendre le template avec les informations de l'utilisateur
        return $this->render('profil/profil.html.twig', [
            'user' => $user,
            'ListEventCreated' => $ListEventCreated,
            'ListEventsInscribed' => $ListEventsInscribed,
        ]);
    }

    #[Route('/edit_profil', name: 'edit_profil')]
    public function editProfil(Request $request,ManagerRegistry $managerRegistry, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();

        // Création du formulaire
        $form = $this->createForm(UserFormType::class, $user);

        // Gestion de la soumission du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si un nouveau mot de passe est saisi
            $plainPassword = $form->get('password')->getData();
            if ($plainPassword) {
                // Hasher le nouveau mot de passe
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);

                // Confirmation du changement de mot de passe
                $this->addFlash('success', 'Your password has been updated.');
            }

            // Enregistrer les modifications de l'utilisateur
            $entityManager = $managerRegistry->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // Rediriger vers la page de profil après modification
            return $this->redirectToRoute('profil_user');
        }

        // Rendre le template avec le formulaire
        return $this->render('profil/edit_profil.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/addUser/{eventId}', name: 'addUser')]
    #[IsGranted('ROLE_USER')]
    public function addUser(int $eventId, EntityManagerInterface $entityManager, EventRepository $eventRepository, MailJetService $mailJetService): Response
    {
        // Récupérer l'événement par son ID
        $event = $eventRepository->find($eventId);

        if (!$event) {
            throw $this->createNotFoundException('L\'événement avec l\'ID ' . $eventId . ' n\'existe pas.');
        }

        // Utiliser le service pour vérifier le nombre maximal de participants
        $remainingPlaces = $this->eventCapacityCalculator->calculateRemainingPlaces($event);

        if ($remainingPlaces <= 0) {
            $this->addFlash('error', 'Le nombre maximal de participants est atteint pour cet événement.');
        } else {
            // Récupérer l'utilisateur connecté
            $user = $this->getUser();

            // Vérifier si l'utilisateur est déjà inscrit à l'événement
            if (!$event->getParticipant()->contains($user)) {
                // Ajouter l'utilisateur à l'événement
                $event->addListUser($user);

                // Persist l'événement
                $entityManager->persist($event);
                $entityManager->flush();

                // Envoyer l'email de confirmation d'inscription
                $mailJetService->sendRegistrationConfirmation($user->getEmail());

                // Ajouter un message flash de succès
                $this->addFlash('success', 'Vous êtes inscrit à l\'événement avec succès.');
            } else {
                // Ajouter un message flash d'erreur
                $this->addFlash('error', 'Vous êtes déjà inscrit à cet événement.');
            }
        }

        // Rediriger vers la vue d'accueil
        return $this->redirectToRoute('accueil');
    }

    #[Route('/removeEventCreated/{eventId}', name: 'removeEventCreated')]
    #[IsGranted('ROLE_USER')]
    public function removeEventCreated($eventId, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur actuel
        $currentUser = $this->getUser();

        // Récupérer l'événement à supprimer
        $event = $entityManager->getRepository(Event::class)->find($eventId);

        // Vérifier si l'événement existe
        if (!$event) {
            throw $this->createNotFoundException('Event not found');
        }

        // Vérifier si l'utilisateur actuel est le créateur de l'événement
        $this->denyAccessUnlessGranted('POST_DELETE', $event);

        // Vérifier l'autorisation avec le Voter
//        $this->denyAccessUnlessGranted(UserVoter::DELETE, $event);

        // Supprimer l'événement de la liste des événements créés par l'utilisateur
        $currentUser->removeEventCreated($event);

        // Supprimer l'événement de la base de données
        $entityManager->remove($event);
        $entityManager->flush();

        // Redirection vers une autre page après la suppression, par exemple le profil de l'utilisateur
        return $this->redirectToRoute('accueil');
    }
}
