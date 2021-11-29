<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Workspace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Company|null find($id, $lockMode = null, $lockVersion = null)
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[]    findAll()
 * @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    public function save(Company $company)
    {
        $this->_em->persist($company);
        $this->_em->flush();
    }

    public function delete(Company $company)
    {
        $this->_em->remove($company);
        $this->_em->flush();
    }

    /** @return Company[] */
    public function findAllByWorkspaceAlphabetically(Workspace $workspace): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.workspace = :id')
            ->setParameter(':id', $workspace->getId())
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function createFindByWorskpaceQueryBuilder(
        Workspace $workspace,
        ?string $search = null,
        ?string $industry = null,
        ?string $order = null,
    ): QueryBuilder {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.workspace = :id')
            ->setParameter(':id', $workspace->getId());

        if ($search) {
            $qb->andWhere('c.name LIKE :search')
                ->setParameter(':search', '%' . $search . '%');
        }

        if ($industry) {
            $qb->andWhere('c.industry = :industry')
                ->setParameter(':industry', $industry);
        }

        switch ($order) {
            case 'date-desc':
                $qb->orderBy('c.createdAt', 'DESC');
                break;
            case 'date-asc':
                $qb->orderBy('c.createdAt', 'ASC');
                break;
            case 'name-desc':
                $qb->orderBy('c.name', 'DESC');
                break;
            case 'name-asc':
                $qb->orderBy('c.name', 'ASC');
                break;
            default:
                $qb->orderBy('c.createdAt', 'DESC');
                break;
        }

        return $qb;
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

    public function findCountFromLastYearByMonth(Workspace $workspace)
    {
        return $this->createQueryBuilder('c')
            ->select('count(c.id) AS dCount, MONTH(c.createdAt) AS dMonth')
            ->groupBy('dMonth')
            ->andWhere('c.createdAt >= :lastYear')
            ->setParameter(':lastYear', new \DateTime('last year'))
            ->andWhere('c.workspace = :id')
            ->setParameter(':id', $workspace->getId())
            ->getQuery()
            ->getResult();
    }
}
