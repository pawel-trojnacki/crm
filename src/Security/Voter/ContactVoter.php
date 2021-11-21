<?php

namespace App\Security\Voter;

use App\Entity\Contact;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ContactVoter extends Voter
{
    public const VIEW = 'CONTACT_VIEW';
    public const EDIT = 'CONTACT_EDIT';

    public function __construct(
        private Security $security,
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [])
            && $subject instanceof \App\Entity\Contact;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Contact $contact */
        $contact = $subject;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($contact, $user);
                break;
        }

        return false;
    }

    private function canView(Contact $contact, User $user): bool
    {
        $workspace = $user->getWorkspace();

        return $workspace === $contact->getWorkspace() && $this->security->isGranted('ROLE_USER');
    }

    private function canEdit(Contact $contact, User $user): bool
    {
        if (!$this->canView($contact, $user)) {
            return false;
        }

        return $this->security->isGranted('ROLE_MANAGER');
    }
}
