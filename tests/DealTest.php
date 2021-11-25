<?php

namespace App\Tests;

use App\Entity\Company;
use App\Entity\Deal;
use App\Entity\User;
use App\Repository\CompanyRepository;
use App\Repository\DealRepository;
use App\Repository\UserRepository;
use App\Repository\WorkspaceRepository;
use App\Tests\Helper\UserTestHelper;
use App\Tests\Helper\WorkspaceTestHelper;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DealTest extends KernelTestCase
{
    private DealRepository $dealRepository;
    private WorkspaceRepository $workspaceRepository;
    private CompanyRepository $companyRepository;
    private UserRepository $userRepository;
    private EntityManager $em;

    public function setUp(): void
    {
        $kernel = self::bootKernel();

        $container = self::getContainer();

        DatabasePrimer::prime($kernel);

        $this->dealRepository = $container->get(DealRepository::class);
        $this->workspaceRepository = $container->get(WorkspaceRepository::class);
        $this->companyRepository = $container->get(CompanyRepository::class);
        $this->userRepository = $container->get(UserRepository::class);
        $this->em = $container->get('doctrine.orm.entity_manager');
    }

    public function createDefaultDeal(): void
    {
        $workspace = WorkspaceTestHelper::createDefaultWorkspace();
        $this->workspaceRepository->save($workspace);

        $user = UserTestHelper::createDefaultUser($workspace);
        $user->addRole('ROLE_ADMIN');

        $plainPassword = '12345678';
        $this->userRepository->register($user, $plainPassword);

        $company = new Company();
        $company->setName('Random Company Inc');
        $company->setWorkspace($workspace);

        $this->companyRepository->save($company);

        $deal = new Deal();
        $deal->setName('Some Deal');
        $deal->setDescription('Lorem ipsum dolor sit amet.');
        $deal->setStage(Deal::STAGES[0]);
        $deal->setWorkspace($workspace);
        $deal->setCompany($company);
        $deal->setCreator($user);

        $this->dealRepository->save($deal);

        $this->em->clear();
    }

    public function testDealIsPropperlyCreatedInDatabase(): void
    {
        $this->createDefaultDeal();

        $deal = $this->dealRepository->findOneBy(['name' => 'Some Deal']);

        // Basic properties
        $this->assertInstanceOf(Deal::class, $deal);
        $this->assertIsInt($deal->getId());
        $this->assertSame('Lorem ipsum dolor sit amet.', $deal->getDescription());
        $this->assertSame('some-deal', $deal->getSlug());
        $this->assertSame(Deal::STAGES[0], $deal->getStage());

        // Relations
        $this->assertSame(
            $deal->getWorkspace(),
            $this->workspaceRepository->findOneBy(['name' => 'Some Workspace'])
        );

        $this->assertSame(
            $deal->getCreator(),
            $this->userRepository->findOneBy(['email' => UserTestHelper::DEFAULTS['email']])
        );

        $this->assertSame(
            $deal->getCompany(),
            $this->companyRepository->findOneBy(['name' => 'Random Company Inc'])
        );
    }

    public function testDealIsRemovedFromDatabase(): void
    {
        $this->createDefaultDeal();

        $deal = $this->dealRepository->findOneBy(['name' => 'Some Deal']);

        $this->assertInstanceOf(Deal::class, $deal);

        $this->dealRepository->delete($deal);

        $this->assertNull(
            $this->dealRepository->findOneBy(['name' => 'Some Deal'])
        );
    }

    public function testUsersAreAssignedAndRemovedFromDeal(): void
    {
        $this->createDefaultDeal();

        $workspace = $this->workspaceRepository->findOneBy(['name' => 'Some Workspace']);

        $user1 = new User();
        $user1->setWorkspace($workspace);
        $user1->setFirstName('Johny');
        $user1->setLastName('Doe');
        $user1->setEmail('johny@doe.com');
        $this->userRepository->register($user1, 'plain password');

        $user2 = new User();
        $user2->setFirstName('Jane');
        $user2->setLastName('Doe');
        $user2->setEmail('jane@doe.com');
        $user2->setWorkspace($workspace);
        $this->userRepository->register($user2, 'plain password');

        $deal = $this->dealRepository->findOneBy(['name' => 'Some Deal']);

        // Adding users to the deal
        $deal->addUser($user1);
        $deal->addUser($user2);

        $this->dealRepository->save($deal);

        $this->assertCount(2, $deal->getUsers());
        $this->assertSame(
            $deal->getUsers()[0],
            $this->userRepository->findOneBy(['email' => 'johny@doe.com'])
        );
        $this->assertSame(
            $deal->getUsers()[1],
            $this->userRepository->findOneBy(['email' => 'jane@doe.com'])
        );

        // Removing user from the deal
        $deal->removeUser($user1);

        $this->assertCount(1, $deal->getUsers());
        $this->assertNotContains(
            $this->userRepository->findOneBy(['email' => 'johny@doe.com']),
            $deal->getUsers(),
        );
    }

    public function testDealIsDeletedWhenRelatedComapnyIsDeleted(): void
    {
        $this->createDefaultDeal();

        $this->assertInstanceOf(
            Deal::class,
            $this->dealRepository->findOneBy(['name' => 'Some Deal'])
        );

        $company = $this->companyRepository->findOneBy(['name' => 'Random Company Inc']);

        $this->companyRepository->delete($company);

        $this->assertNull(
            $this->dealRepository->findOneBy(['name' => 'Some Deal'])
        );
    }

    public function testDealIsDeletedWhenRelatedWorkspaceIsDeleted(): void
    {
        $this->createDefaultDeal();

        $this->assertInstanceOf(
            Deal::class,
            $this->dealRepository->findOneBy(['name' => 'Some Deal'])
        );

        $workspace = $this->workspaceRepository->findOneBy(['name' => 'Some Workspace']);

        $this->workspaceRepository->delete($workspace);

        $this->assertNull(
            $this->dealRepository->findOneBy(['name' => 'Some Deal'])
        );
    }
}
