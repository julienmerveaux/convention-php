<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Event;

class UserVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';
    public const DELETE = 'POST_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Vérifier si l'attribut est supporté et si le sujet est une instance de User ou Event
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && ($subject instanceof UserInterface || $subject instanceof Event);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // Si l'utilisateur est anonyme, refuser l'accès
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Vérifier si l'utilisateur a le rôle ROLE_USER
        $hasUserRole = in_array('ROLE_USER', $user->getRoles());

        switch ($attribute) {
            case self::EDIT:
            case self::VIEW:
                // Autoriser l'édition ou la visualisation si l'utilisateur a le rôle ROLE_USER ou c'est son propre profil
                return $hasUserRole || $user === $subject;

            case self::DELETE:
                // Pour la suppression, vérifier si le sujet est un événement
                if ($subject instanceof Event) {
                    // Autoriser la suppression si l'utilisateur a le rôle ROLE_USER et est le créateur de l'événement
                    return $hasUserRole && $subject->getCreator() === $user;
                }
                return false;
        }

        return false;
    }
}
