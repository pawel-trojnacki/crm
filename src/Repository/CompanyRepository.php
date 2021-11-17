<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Workspace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
}