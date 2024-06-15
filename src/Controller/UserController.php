<?php

namespace App\Controller;

use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class UserController extends AbstractController
{
    #[Route('/profil_user', name: 'profil_user')]
    public function index(): \Symfony\Component\HttpFoundation\Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Vérifier que l'utilisateur est bien connecté
        if (!$user instanceof UserInterface) {
            return $this->redirectToRoute('login');
        }

        // Rendre le template avec les informations de l'utilisateur
        return $this->render('profil/profil.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/edit_profil', name: 'edit_profil')]
    public function editProfil(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Création du formulaire
        $form = $this->createForm(UserFormType::class, $user);

        // Gestion de la soumission du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Hasher le nouveau mot de passe si fourni
            $newPassword = $form->get('password')->getData();
            if ($newPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }

            // Enregistrer les modifications de l'utilisateur
            $entityManager->persist($user);
            $entityManager->flush();

            // Rediriger vers la page de profil après modification
            return $this->redirectToRoute('profil_user');
        }

        // Rendre le template avec le formulaire
        return $this->render('profil/edit_profil.html.twig', [
            'form' => $form->createView(),
        ]);
    }}
