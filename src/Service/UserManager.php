<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Workspace;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function register(User $user, string $plainPassword, Workspace $workspace)
    {
        $password = $this->passwordHasher->hashPassword($user, $plainPassword);

        $user->setPassword($password);

        $user->setWorkspace($workspace);

        $this->userRepository->save($user);
    }

    public function setAdminRole(User $user): void
    {
        $roles = $user->getRoles();

        $roles[] = 'ROLE_ADMIN';

        $user->setRoles($roles);
    }
}
