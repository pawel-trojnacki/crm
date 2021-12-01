<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileVoter extends Voter
{
    public const VIEW = 'PROFILE_VIEW';
    public const EDIT = 'PROFILE_EDIT';

    public function __construct(
        private Security $security,
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT])
            && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var User $member */
        $member = $subject;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::VIEW:
                return $this->canView($member, $user);
                break;
            case self::EDIT:
                return $this->canEdit($member, $user);
                break;
        }

        return false;
    }

    private function canView(User $member, User $user): bool
    {
        return $member === $user && $this->security->isGranted('ROLE_USER');
    }

    private function canEdit(User $member, User $user): bool
    {
        if (!$this->canView($member, $user)) {
            return false;
        }

        return $this->security->isGranted('ROLE_MANAGER');
    }
}
