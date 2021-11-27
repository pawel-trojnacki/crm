<?php

namespace App\Repository;

use App\Entity\Abstract\AbstractNoteEntity;
use App\Entity\DealNote;
use App\Repository\Interface\NoteRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DealNote|null find($id, $lockMode = null, $lockVersion = null)
 * @method DealNote|null findOneBy(array $criteria, array $orderBy = null)
 * @method DealNote[]    findAll()
 * @method DealNote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DealNoteRepository extends ServiceEntityRepository implements NoteRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DealNote::class);
    }

    public function save(AbstractNoteEntity $dealNote): void
    {
        $this->_em->persist($dealNote);
        $this->_em->flush();
    }

    public function delete(AbstractNoteEntity $dealNote): void
    {
        $this->_em->remove($dealNote);
        $this->_em->flush();
    }
}
