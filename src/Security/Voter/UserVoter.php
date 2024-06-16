<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';
    public const DELETE = 'POST_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof \App\Entity\User;
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
                // Autoriser l'édition si l'utilisateur a le rôle ROLE_USER ou édite son propre profil
                return $hasUserRole || $user === $subject;
                break;

            case self::VIEW:
                // Autoriser la visualisation si l'utilisateur a le rôle ROLE_USER, c'est son propre profil, ou il est admin
                return $hasUserRole || $user === $subject;
                break;

            case self::DELETE:
                // Autoriser la suppression si l'utilisateur a le rôle ROLE_USER et est le créateur de l'événement
                if ($hasUserRole && $subject instanceof \App\Entity\Event) {
                    return $subject->getCreator() === $user;
                }
                return false;
                break;
        }

        return false;
    }
}
