<?php

namespace App\Repository;

use App\Entity\Industry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Industry|null find($id, $lockMode = null, $lockVersion = null)
 * @method Industry|null findOneBy(array $criteria, array $orderBy = null)
 * @method Industry[]    findAll()
 * @method Industry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IndustryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Industry::class);
    }

    public function save(Industry $industry)
    {
        $this->_em->persist($industry);
        $this->_em->flush();
    }

    public function delete(Industry $industry)
    {
        $this->_em->remove($industry);
        $this->_em->flush();
    }

    /** @return Industry[] */
    public function findAllAlphabetically(): array
    {
        return $this->createQueryBuilder('i')
            ->orderBy('i.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
