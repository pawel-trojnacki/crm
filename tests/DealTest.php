<?php

namespace App\Tests;

use App\Entity\Company;
use App\Entity\Deal;
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

        $company = new Company($workspace, $user, 'Random Company Inc');

        $this->companyRepository->save($company);

        $deal = new Deal(
            $workspace, 
            $user,
            'Some Deal',
            Deal::STAGES[0],
            $company,
            null,
            'Lorem ipsum dolor sit amet'
        );

        $this->dealRepository->save($deal);

        $this->em->clear();
    }

    public function testDealIsPropperlyCreatedInDatabase(): void
    {
        $this->createDefaultDeal();

        $deal = $this->dealRepository->findOneBy(['name' => 'Some Deal']);

        // Basic properties
        $this->assertInstanceOf(Deal::class, $deal);
        $this->assertIsString($deal->getId());
        $this->assertSame('Lorem ipsum dolor sit amet', $deal->getDescription());
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
