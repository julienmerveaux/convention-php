<?php

// src/Security/Voter/EventVoter.php

namespace App\Security\Voter;

use App\Entity\Event;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class EventVoter extends Voter
{
    public const EDIT = 'EVENT_EDIT';
    public const DELETE = 'EVENT_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Event;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Event $event */
        $event = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($event, $user);

            case self::DELETE:
                return $this->canDelete($event, $user);
        }

        return false;
    }

    private function canEdit(Event $event, User $user): bool
    {
        return $user === $event->getCreator();
    }

    private function canDelete(Event $event, User $user): bool
    {
        return $user === $event->getCreator();
    }
}
