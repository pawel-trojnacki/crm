<?php

namespace App\Tests;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use App\Repository\WorkspaceRepository;
use App\Tests\Helper\WorkspaceTestHelper;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CompanyTest extends KernelTestCase
{
    private CompanyRepository $companyRepository;
    private WorkspaceRepository $workspaceRepository;
    private EntityManager $em;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $container = self::getContainer();

        DatabasePrimer::prime($kernel);

        $this->companyRepository = $container->get(CompanyRepository::class);
        $this->workspaceRepository = $container->get(WorkspaceRepository::class);
        $this->em = $container->get('doctrine.orm.entity_manager');
    }

    public function testCompanyIsCorrectlyCreatedAndRemovedFromDatabase(): void
    {
        $workspace = WorkspaceTestHelper::createDefaultWorkspace();

        $this->workspaceRepository->save($workspace);

        $company = new Company();
        $company->setName('Some Company');
        $company->setWorkspace($workspace);

        $this->companyRepository->save($company);

        $savedCompany = $this->companyRepository->findOneBy([
            'name' => 'Some Company',
        ]);

        $this->assertInstanceOf(Company::class, $savedCompany);
        $this->assertIsInt($savedCompany->getId());
        $this->assertSame($workspace, $savedCompany->getWorkspace());

        $this->companyRepository->delete($savedCompany);

        $this->assertNull(
            $this->companyRepository->findOneBy([
                'name' => 'Some Company'
            ])
        );
    }

    public function testCompaniesAreFetchedOrderedAlphabetically(): void
    {
        $workspace = WorkspaceTestHelper::createDefaultWorkspace();

        $this->workspaceRepository->save($workspace);

        foreach (['Microsoft', 'Apple', 'Netflix'] as $name) {
            $company = new Company();
            $company->setName($name);
            $company->setWorkspace($workspace);

            $this->companyRepository->save($company);
        }

        $savedCompanies = $this->companyRepository->findAllByWorkspaceAlphabetically($workspace);

        // Companies correctly fetched
        $this->assertIsArray($savedCompanies);
        $this->assertSame(3, sizeof($savedCompanies));

        // Companies ordered alphabetically by name
        $this->assertSame('Apple', $savedCompanies[0]->getName());
        $this->assertSame('Microsoft', $savedCompanies[1]->getName());
    }

    public function testCompanyIsDeletedFromDatabaseWhenRelatedWorkspaceIsDeleted(): void
    {
        $workspace = WorkspaceTestHelper::createDefaultWorkspace();

        $this->workspaceRepository->save($workspace);

        $company = new Company();
        $company->setName('Some Company');
        $company->setWorkspace($workspace);

        $this->companyRepository->save($company);

        $this->em->clear();

        $this->assertInstanceOf(
            Company::class,
            $this->companyRepository->findOneBy([
                'name' => 'Some Company',
            ])
        );

        $savedWorkspace = $this->workspaceRepository->findOneBy([
            'name' => 'Some Workspace',
        ]);

        $this->workspaceRepository->delete($savedWorkspace);

        $this->assertNull(
            $this->companyRepository->findOneBy([
                'name' => 'Some Company',
            ])
        );
    }
}
