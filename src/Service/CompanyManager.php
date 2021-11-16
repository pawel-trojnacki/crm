<?php

namespace App\Service;

use App\Entity\Workspace;
use App\Repository\CompanyRepository;

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
}
