<?php

namespace App\Repository;

use App\Entity\Contact;
use App\Entity\Workspace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Contact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contact[]    findAll()
 * @method Contact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    public function save(Contact $contact): void
    {
        $this->_em->persist($contact);
        $this->_em->flush();
    }

    public function delete(Contact $contact): void
    {
        $this->_em->remove($contact);
        $this->_em->flush();
    }

    public function createFindByWorkspaceQueryBuilder(
        Workspace $workspace,
        ?string $search = null,
        ?int $userId = null,
        ?string $order = null,
    ): QueryBuilder {
        return $this->createFiltersQueryBuilder($search, $userId, $order)
            ->andWhere('c.workspace = :id')
            ->setParameter(':id', $workspace->getId());
    }

    public function findAllCountByWorkspace(Workspace $workspace): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->andWhere('c.workspace = :id')
            ->setParameter(':id', $workspace->getId())
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findCountFromLastYearByMonth(Workspace $workspace): array
    {
        return $this->createQueryBuilder('c')
            ->select('count(c.id) AS eCount, MONTH(c.createdAt) AS eMonth')
            ->groupBy('eMonth')
            ->andWhere('c.createdAt >= :lastYear')
            ->setParameter(':lastYear', new \DateTime('last year'))
            ->andWhere('c.workspace = :id')
            ->setParameter(':id', $workspace->getId())
            ->getQuery()
            ->getResult();
    }

    private function createFiltersQueryBuilder(
        ?string $search = null,
        ?int $userId = null,
        ?string $order = null,
    ): QueryBuilder {
        $qb = $this->createQueryBuilder('c');

        if ($search) {
            $qb->andWhere('c.firstName LIKE :search OR c.lastName LIKE :search')
                ->setParameter(':search', '%' . $search . '%');
        }

        if ($userId) {
            $qb->andWhere('c.creator = :userId')
                ->setParameter(':userId', $userId);
        }

        switch ($order) {
            case 'date-desc':
                $qb->orderBy('c.createdAt', 'DESC');
                break;
            case 'date-asc':
                $qb->orderBy('c.createdAt', 'ASC');
                break;
            case 'name-desc':
                $qb->orderBy('c.firstName', 'DESC');
                break;
            case 'name-asc':
                $qb->orderBy('c.firstName', 'ASC');
                break;
            case 'surname-desc':
                $qb->orderBy('c.lastName', 'DESC');
                break;
            case 'surname-asc':
                $qb->orderBy('c.lastName', 'ASC');
                break;
            default:
                $qb->orderBy('c.createdAt', 'DESC');
                break;
        }

        return $qb;
    }
}
