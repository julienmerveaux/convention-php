<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface; // Importez LoggerInterface

class MailJetService
{
    private MailerInterface $mailer;
    private LoggerInterface $logger; // Déclarez le logger
    public function __construct(MailerInterface $mailer, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    public function sendEmail(string $to, string $subject, string $text): void
    {
        $email = (new Email())
            ->from('jaimet@outlook.fr')
            ->to($to)
            ->subject($subject)
            ->text($text);

        try {
            $this->logger->debug("Tentative d'envoi d'email à $to");
            $this->mailer->send($email);
            $this->logger->info("Email envoyé à $to avec le sujet $subject");
        } catch (\Exception $e) {
            $this->logger->error("Erreur lors de l'envoi de l'email : " . $e->getMessage());
        }
    }

    public function sendRegistrationConfirmation(string $to): void
    {
        $subject = "Confirmation d'inscription";
        $content = "Vous êtes inscrit à l'événement.";
        $this->sendEmail($to, $subject, $content);
    }

    public function sendCancellationConfirmation(string $to): void
    {
        $subject = "Confirmation d'annulation";
        $content = "Vous avez annulé votre inscription à l'évènement.";
        $this->sendEmail($to, $subject, $content);
    }

}
