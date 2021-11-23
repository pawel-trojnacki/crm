<?php

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\WorkspaceRepository;
use App\Tests\Helper\UserTestHelper;
use App\Tests\Helper\WorkspaceTestHelper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    private UserRepository $userRepository;
    private WorkspaceRepository $workspaceRepository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $container = self::getContainer();

        DatabasePrimer::prime($kernel);

        $this->userRepository = $container->get(UserRepository::class);
        $this->workspaceRepository = $container->get(WorkspaceRepository::class);
    }

    private function registerDefaultUser(): void {
        $workspace = WorkspaceTestHelper::createDefaultWorkspace();

        $this->workspaceRepository->save($workspace);

        $user = UserTestHelper::createDefaultUser($workspace);
        
        $user->addRole('ROLE_ADMIN');

        $plainPassword = '12345678';

        $this->userRepository->register($user, $plainPassword);
    }

    public function testUserIsPropperlyRegistered(): void {
        $this->registerDefaultUser();

        $user = $this->userRepository->findOneBy(['email' => UserTestHelper::DEFAULTS['email']]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertIsInt($user->getId());
        $this->assertSame(UserTestHelper::DEFAULTS['firstName'], $user->getFirstName());
        $this->assertSame(UserTestHelper::DEFAULTS['lastName'], $user->getLastName());
        $this->assertIsArray($user->getRoles());
        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertIsString($user->getPassword());
        $this->assertNotEquals('12345678', $user->getPassword());
        $this->assertInstanceOf('DateTime', $user->getCreatedAt());
        $this->assertInstanceOf('DateTime', $user->getUpdatedAt());
    }

    public function testUserIsRemovedFromDatabase(): void
    {
        $this->registerDefaultUser();

        $user = $this->userRepository->findOneBy(['email' => UserTestHelper::DEFAULTS['email']]);

        $this->userRepository->delete($user);

        $this->assertNull(
            $this->userRepository->findOneBy(['email' => UserTestHelper::DEFAULTS['email']])
        );
    }
}
