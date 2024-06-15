<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventFormType;
use App\Repository\EventRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class EventController extends AbstractController
{


    #[Route('/', name: 'accueil')]
    public function index(Request $request, EventRepository $repository): Response
    {
        $is_connected = false;
        $user = $this->getUser();
        if ($user instanceof UserInterface) {
            $is_connected = true;
        }
        $title = $request->query->get('title', '');
        $Event = $repository->findByTitleLike($is_connected, $title);

        return $this->render('Accueil.html.twig', [
            'events' => $Event,
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


    #[Route('/createEvent', name: 'createEvent')]
    public function new(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $managerRegistry->getManager();
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('accueil');
        }

        return $this->render('new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/event/{id}/inscription", name="inscription_event")
     */
    public function inscription(int $id): Response
    {
        // Logique pour gérer l'inscription à l'événement avec l'ID $id
        // Par exemple, ajouter l'utilisateur actuel à la liste des participants de l'événement

        // Redirection vers une autre page après l'inscription
        return $this->redirectToRoute('event_details', ['id' => $id]);
    }
}
