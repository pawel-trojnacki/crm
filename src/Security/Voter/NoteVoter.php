<?php

namespace App\Security\Voter;

use App\Entity\Abstract\AbstractNoteEntity;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class NoteVoter extends Voter
{
    public const EDIT = 'NOTE_EDIT';

    public function __construct(
        private Security $security,
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::EDIT])
            && $subject instanceof AbstractNoteEntity;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var AbstractNoteEntity $cnote */
        $note = $subject;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($note, $user);
                break;
        }

        return false;
    }

    private function canEdit(AbstractNoteEntity $note, User $user): bool
    {
        $parent = $note->getParent();
        $workspace = $parent->getWorkspace();

        if (!$workspace === $user->getWorkspace()) {
            return false;
        }

        return $this->security->isGranted('ROLE_ADMIN') ||
            $this->security->isGranted('ROLE_MANAGER') && $note->getCreator() === $user;
    }
}
