<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class AuthentificatorController extends AbstractController
{
    #[Route('/login', name: 'app_authentificator')]
    public function login(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/AuthentificatorController.php',
        ]);
    }

    #[Route('/register', name: 'app_authentificator')]
    public function register(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/AuthentificatorController.php',
        ]);
    }
}
