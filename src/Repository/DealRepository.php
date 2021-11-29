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

    public function createFindByWorkspaceQueryBuilder(
        Workspace $workspace,
        ?string $search = null,
        ?string $stage = null,
        ?string $order = null

    ): QueryBuilder {
        $qb = $this->createQueryBuilder('d')
            ->andWhere('d.workspace = :id')
            ->setParameter(':id', $workspace->getId());

        if ($search) {
            $qb->andWhere('d.name LIKE :search')
                ->setParameter(':search', '%' . $search . '%');
        }

        if ($stage) {
            $qb->andWhere('d.stage = :stage')
                ->setParameter(':stage', $stage);
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

    public function findAllCountByWorkspace(Workspace $workspace): int
    {
        return $this->createQueryBuilder('d')
            ->select('COUNT(d.id)')
            ->andWhere('d.workspace = :id')
            ->setParameter(':id', $workspace->getId())
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findCountFromLastYearByMonth(Workspace $workspace)
    {
        return $this->createQueryBuilder('d')
            ->select('count(d.id) AS dCount, MONTH(d.createdAt) AS dMonth')
            ->groupBy('dMonth')
            ->andWhere('d.createdAt >= :lastYear')
            ->setParameter(':lastYear', new \DateTime('last year'))
            ->andWhere('d.workspace = :id')
            ->setParameter(':id', $workspace->getId())
            ->getQuery()
            ->getResult();
    }
}
