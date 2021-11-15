<?php

namespace App\Service;

use App\Entity\Workspace;
use App\Repository\ContactRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

class ContactManager
{
    public function __construct(
        private ContactRepository $contactRepository,
    ) {
    }

    public function createPager(
        Workspace $workspace,
        int $currentPage,
        mixed $order,
        mixed $search,
    ): Pagerfanta {
        $qb = $this->contactRepository->createPagerQueryBuilder($workspace, $order, $search);

        $adapter = new QueryAdapter($qb);

        $pager = new Pagerfanta($adapter);

        $pager->setMaxPerPage(25);
        $pager->setCurrentPage($currentPage);

        return $pager;
    }
}
