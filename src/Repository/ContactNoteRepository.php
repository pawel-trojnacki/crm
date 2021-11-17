<?php

namespace App\Repository;

use App\Entity\ContactNote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContactNote|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactNote|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactNote[]    findAll()
 * @method ContactNote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactNoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactNote::class);
    }

    public function save(ContactNote $contactNote)
    {
        $this->_em->persist($contactNote);
        $this->_em->flush();
    }

    public function delete(ContactNote $contactNote)
    {
        $this->_em->remove($contactNote);
        $this->_em->flush();
    }
}
