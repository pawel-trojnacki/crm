<?php

namespace App\Repository;

use App\Entity\Deal;
use App\Entity\User;
use App\Entity\Workspace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Deal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Deal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Deal[]    findAll()
 * @method Deal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DealRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Deal::class);
    }

    public function save(Deal $deal): void
    {
        $this->_em->persist($deal);
        $this->_em->flush();
    }

    public function delete(Deal $deal): void
    {
        $this->_em->remove($deal);
        $this->_em->flush();
    }

    private function createFiltersQueryBuilder(
        ?string $search = null,
        ?string $stage = null,
        ?int $userId = null,
        ?string $order = null,
    ): QueryBuilder {
        $qb = $this->createQueryBuilder('d');

        if ($search) {
            $qb->andWhere('d.name LIKE :search')
                ->setParameter(':search', '%' . $search . '%');
        }

        if ($stage) {
            $qb->andWhere('d.stage = :stage')
                ->setParameter(':stage', $stage);
        }

        if ($userId) {
            $qb->andWhere(':userId MEMBER OF d.users')
                ->setParameter(':userId', $userId);
        }

        switch ($order) {
            case 'date-desc':
                $qb->orderBy('d.createdAt', 'DESC');
                break;
            case 'date-asc':
                $qb->orderBy('d.createdAt', 'ASC');
                break;
            case 'name-desc':
                $qb->orderBy('d.name', 'DESC');
                break;
            case 'name-asc':
                $qb->orderBy('d.name', 'ASC');
                break;
            default:
                $qb->orderBy('d.createdAt', 'DESC');
                break;
        }

        return $qb;
    }

    public function createFindByWorkspaceQueryBuilder(
        Workspace $workspace,
        ?string $search = null,
        ?string $stage = null,
        ?int $userId = null,
        ?string $order = null

    ): QueryBuilder {
        return $this->createFiltersQueryBuilder($search, $stage, $userId, $order)
            ->andWhere('d.workspace = :id')
            ->setParameter(':id', $workspace->getId());
    }

    public function findAllCountByWorkspace(Workspace $workspace): int
    {
        return $this->createQueryBuilder('d')
            ->select('COUNT(d.id)')
            ->andWhere('d.workspace = :id')
            ->setParameter(':id', $workspace->getId())
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findCountGroupByStage(Workspace $workspace): array
    {
        return $this->createQueryBuilder('d')
            ->select('count(d.id) AS dCount, d.stage')
            ->groupBy('d.stage')
            ->andWhere('d.workspace = :id')
            ->setParameter(':id', $workspace->getId())
            ->getQuery()
            ->getResult();
    }

    public function findCountFromLastYearByMonth(Workspace $workspace): array
    {
        return $this->createQueryBuilder('d')
            ->select('count(d.id) AS eCount, MONTH(d.createdAt) AS eMonth')
            ->groupBy('eMonth')
            ->andWhere('d.createdAt >= :lastYear')
            ->setParameter(':lastYear', new \DateTime('last year'))
            ->andWhere('d.workspace = :id')
            ->setParameter(':id', $workspace->getId())
            ->getQuery()
            ->getResult();
    }
}
