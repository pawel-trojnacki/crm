<?php

namespace App\Security\Voter;

use App\Entity\Meeting;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class MeetingVoter extends Voter
{
    public const VIEW = 'MEETING_VIEW';
    public const EDIT = 'MEETING_EDIT';

    public function __construct(
        private Security $security,
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT])
            && $subject instanceof Meeting;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {        
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Meeting $meeting */
        $meeting = $subject;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::VIEW:
                return $this->canView($meeting, $user);
                break;
            case self::EDIT:
                return $this->canEdit($meeting, $user);
                break;
        }

        return false;
    }

    private function canView(Meeting $meeting, User $user): bool
    {
        $workspace = $user->getWorkspace();

        return $workspace === $meeting->getWorkspace() && $this->security->isGranted(User::ROLE_USER);
    }

    private function canEdit(Meeting $meeting, User $user): bool
    {
        if (!$this->canView($meeting, $user)) {
            return false;
        }

        return $this->security->isGranted(User::ROLE_ADMIN) ||
            $this->security->isGranted(User::ROLE_MANAGER) && $meeting->getCreator() === $user;
    }
}
