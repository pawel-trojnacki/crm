<?php

namespace App\Service;

use App\Entity\Industry;
use App\Repository\IndustryRepository;

class IndustryManager
{
    public function __construct(
        private IndustryRepository $industryRepository,
    ) {
    }

    /** @return Industry[] */
    public function findAllAlphabetically(): array
    {
        return $this->industryRepository->findAllAlphabetically();
    }
}
