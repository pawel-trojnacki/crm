<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Workspace;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class WorkspaceVoter extends Voter
{
    public const VIEW = 'WORKSPACE_VIEW';
    public const EDIT = 'WORKSPACE_EDIT';
    public const ADD_ITEM = 'WORKSPACE_ADD_ITEM';

    public function __construct(
        private Security $security,
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::ADD_ITEM])
            && $subject instanceof Workspace;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Workspace $workspace */
        $workspace = $subject;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::VIEW:
                return $this->canView($workspace, $user);
                break;
            case self::EDIT:
                return $this->canEdit($workspace, $user);
                break;
            case self::ADD_ITEM:
                return $this->canAddItem($workspace, $user);
                break;
        }

        return false;
    }

    public function canView(Workspace $workspace, User $user): bool
    {
        return $this->security->isGranted(User::ROLE_USER) && $workspace === $user->getWorkspace();
    }

    public function canEdit(Workspace $workspace, User $user): bool
    {
        if (!$this->canView($workspace, $user)) {
            return false;
        }

        return $this->security->isGranted(User::ROLE_ADMIN);
    }

    public function canAddItem(Workspace $workspace, User $user): bool
    {
        if (!$this->canView($workspace, $user)) {
            return false;
        }

        return $this->security->isGranted(User::ROLE_MANAGER);
    }
}
