<?php

namespace App\Security\Voter;

use App\Entity\Company;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CompanyVoter extends Voter
{
    public const VIEW = 'COMPANY_VIEW';
    public const EDIT = 'COMPANY_EDIT';

    public function __construct(
        private Security $security,
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT])
            && $subject instanceof Company;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Company $company */
        $company = $subject;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::VIEW:
                return $this->canView($company, $user);
                break;
            case self::EDIT:
                return $this->canEdit($company, $user);
                break;
        }

        return false;
    }

    private function canView(Company $company, User $user): bool
    {
        $workspace = $user->getWorkspace();

        return $workspace === $company->getWorkspace() && $this->security->isGranted(User::ROLE_USER);
    }

    private function canEdit(Company $company, User $user): bool
    {
        if (!$this->canView($company, $user)) {
            return false;
        }

        return $this->security->isGranted(User::ROLE_ADMIN) ||
            $this->security->isGranted(User::ROLE_MANAGER) && $company->getCreator() === $user;
    }
}
