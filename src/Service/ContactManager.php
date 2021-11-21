<?php

namespace App\Service;

use App\Entity\Contact;
use App\Entity\User;
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

    public function save(Contact $contact, Workspace $workspace, User $user): void
    {
        $contact->setWorkspace($workspace);
        $contact->setCreator($user);

        $this->contactRepository->save($contact);
    }

    public function update(Contact $contact): void
    {
        $this->contactRepository->save($contact);
    }

    public function delete(Contact $contact): void
    {
        $this->contactRepository->delete($contact);
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

    public function findOneBySlug(string $slug): ?Contact
    {
        return $this->contactRepository->findOneBy(['slug' => $slug]);
    }
}
