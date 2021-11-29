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
        ?string $order = null,
        ?string $search = null,
    ): QueryBuilder {

        $qb = $this->createQueryBuilder('q')
            ->andWhere('q.workspace = :id')
            ->setParameter(':id', $workspace->getId());

        if ($search) {
            $qb->andWhere('q.firstName LIKE :search OR q.lastName LIKE :search')
                ->setParameter(':search', '%' . $search . '%');
        }

        switch ($order) {
            case 'date-desc':
                $qb->orderBy('q.createdAt', 'DESC');
                break;
            case 'date-asc':
                $qb->orderBy('q.createdAt', 'ASC');
                break;
            case 'name-desc':
                $qb->orderBy('q.firstName', 'DESC');
                break;
            case 'name-asc':
                $qb->orderBy('q.firstName', 'ASC');
                break;
            case 'surname-desc':
                $qb->orderBy('q.lastName', 'DESC');
                break;
            case 'surname-asc':
                $qb->orderBy('q.lastName', 'ASC');
                break;
            default:
                $qb->orderBy('q.createdAt', 'DESC');
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
}
