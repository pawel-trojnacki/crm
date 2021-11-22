<?php

namespace App\Service;

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

class PagerService
{
    public function createPager(QueryBuilder $qb, int $currentPage, ?int $maxResults = 25): Pagerfanta
    {
        $adapter = new QueryAdapter($qb);

        $pager = new Pagerfanta($adapter);

        $pager->setMaxPerPage($maxResults);
        $pager->setCurrentPage($currentPage);

        return $pager;
    }
}
