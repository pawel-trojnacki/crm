<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\Workspace;
use App\Repository\CompanyRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormInterface;

class CompanyManager
{
    public function __construct(
        private CompanyRepository $companyRepository,
    ) {
    }

    /** @return Company[] */
    public function findAllByWorkspaceAlphabetically(Workspace $workspace): array
    {
        return $this->companyRepository->findAllByWorkspaceAlphabetically($workspace);
    }

    public function createPager(
        Workspace $workspace,
        int $currentPage,
        ?string $search,
        ?string $industry,
        ?string $order,
    ): Pagerfanta {
        $qb = $this->companyRepository->createPagerQueryBuilder($workspace, $search, $industry, $order);

        $adapter = new QueryAdapter($qb);

        $pager = new Pagerfanta($adapter);

        $pager->setMaxPerPage(10);
        $pager->setCurrentPage($currentPage);

        return $pager;
    }

    public function save(Company $company, Workspace $workspace): void
    {
        $company->setWorkspace($workspace);

        $this->companyRepository->save($company);
    }

    public function update(Company $company): void
    {
        $this->companyRepository->save($company);
    }

    public function delete(Company $company): void
    {
        $this->companyRepository->delete($company);
    }
}
