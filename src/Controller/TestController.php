<?php
// src/Controller/TestController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\MailJetService;

class TestController extends AbstractController
{
    #[Route('/test-email', name: 'test_email')]
    public function testEmail(MailJetService $mailJetService): Response
    {
        try {
            $mailJetService->sendRegistrationConfirmation('jaimet@outlook.fr');
            return new Response('Email de test envoyé.');
        } catch (\Exception $e) {
            return new Response('Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
        }
    }
    #[Route('/test-emailCancel', name: 'testEmailCancel')]
    public function testEmailCancel(MailJetService $mailJetService): Response
    {
        try {
            $mailJetService->sendCancellationConfirmation('jaimet@outlook.fr');
            return new Response('Email de test envoyé.');
        } catch (\Exception $e) {
            return new Response('Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
        }
    }
}
