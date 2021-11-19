<?php

namespace App\Tests;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use App\Repository\WorkspaceRepository;
use App\Tests\Helper\WorkspaceTestHelper;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use function Zenstruck\Foundry\faker;

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
        $company->setWebsite('www.example.com');
        $company->setAddress('286 Adah Forest');
        $company->setCity('West Williamberg');

        $this->companyRepository->save($company);

        $savedCompany = $this->companyRepository->findOneBy([
            'name' => 'Some Company',
        ]);

        $this->assertInstanceOf(Company::class, $savedCompany);
        $this->assertIsInt($savedCompany->getId());
        $this->assertSame($workspace, $savedCompany->getWorkspace());
        $this->assertSame('www.example.com', $savedCompany->getWebsite());
        $this->assertSame('286 Adah Forest', $savedCompany->getAddress());
        $this->assertSame('West Williamberg', $savedCompany->getCity());
        $this->assertNull($savedCompany->getCountry());
        $this->assertInstanceOf('DateTime', $savedCompany->getCreatedAt());
        $this->assertInstanceOf('DateTime', $savedCompany->getUpdatedAt());

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

        $savedCompanies =
            $this->companyRepository->findAllByWorkspaceAlphabetically($workspace);

        // Companies correctly fetched
        $this->assertIsArray($savedCompanies);
        $this->assertSame(3, sizeof($savedCompanies));

        // Companies ordered alphabetically by name
        $this->assertSame('Apple', $savedCompanies[0]->getName());
        $this->assertSame('Microsoft', $savedCompanies[1]->getName());
    }

    public function testCompaniesArePropperlySortedByDate(): void
    {
        $workspace = WorkspaceTestHelper::createDefaultWorkspace();

        $this->workspaceRepository->save($workspace);

        for ($i = 0; $i < 3; $i++) {
            $company = new Company();
            $company->setWorkspace($workspace);
            $company->setName(faker()->company());
            $company->setCreatedAt(faker()->dateTimeBetween('-1 month', 'now'));

            $this->companyRepository->save($company);
        }

        /** @var Company[] $savedCompanies */
        $companies = $this->companyRepository->createPagerQueryBuilder(
            workspace: $workspace,
            order: 'date-desc',
        )->getQuery()->getResult();

        $this->assertTrue($companies[0]->getCreatedAt() > $companies[1]->getCreatedAt());
        $this->assertTrue($companies[1]->getCreatedAt() > $companies[2]->getCreatedAt());

        /** @var Company[] $companies */
        $companies = $this->companyRepository->createPagerQueryBuilder(
            workspace: $workspace,
            order: 'date-asc',
        )->getQuery()->getResult();

        $this->assertTrue($companies[0]->getCreatedAt() < $companies[1]->getCreatedAt());
        $this->assertTrue($companies[1]->getCreatedAt() < $companies[2]->getCreatedAt());
    }

    public function testCompaniesAreSearchedByProvidedPhrase(): void
    {
        $workspace = WorkspaceTestHelper::createDefaultWorkspace();

        $this->workspaceRepository->save($workspace);

        foreach (['Microsoft', 'Apple', 'Netflix', 'Google', 'Amazon'] as $name) {
            $company = new Company();
            $company->setName($name);
            $company->setWorkspace($workspace);

            $this->companyRepository->save($company);
        }

        /** @var Company[] $companies */
        $companies = $this->companyRepository->createPagerQueryBuilder(
            workspace: $workspace,
            search: 'app',
        )->getQuery()->getResult();

        $this->assertCount(1, $companies);
        $this->assertSame('Apple', $companies[0]->getName());

        /** @var Company[] $companies */
        $companies = $this->companyRepository->createPagerQueryBuilder(
            workspace: $workspace,
            search: 'o',
        )->getQuery()->getResult();

        $this->assertCount(3, $companies);
        $this->assertContains(
            $this->companyRepository->findOneBy(['name' => 'Google']),
            $companies,
        );
        $this->assertNotContains(
            $this->companyRepository->findOneBy(['name' => 'Netflix']),
            $companies,
        );
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
