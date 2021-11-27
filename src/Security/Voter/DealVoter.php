<?php

namespace App\Security\Voter;

use App\Entity\Deal;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DealVoter extends Voter
{
    public const VIEW = 'DEAL_VIEW';
    public const EDIT = 'DEAL_EDIT';

    public function __construct(
        private Security $security,
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT])
            && $subject instanceof Deal;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Deal $deal */
        $deal = $subject;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::VIEW:
                return $this->canView($deal, $user);
                break;
            case self::EDIT:
                return $this->canEdit($deal, $user);
                break;
        }

        return false;
    }

    private function canView(Deal $deal, User $user): bool
    {
        $workspace = $user->getWorkspace();

        return $workspace === $deal->getWorkspace() && $this->security->isGranted('ROLE_USER');
    }

    private function canEdit(Deal $deal, User $user): bool
    {
        if (!$this->canView($deal, $user)) {
            return false;
        }

        return $this->security->isGranted('ROLE_ADMIN') ||
            $this->security->isGranted('ROLE_MANAGER') && $deal->getCreator() === $user;
    }
}
