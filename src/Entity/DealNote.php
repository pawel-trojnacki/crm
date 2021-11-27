<?php

namespace App\Entity;

use App\Entity\Abstract\AbstractNoteEntity;
use App\Entity\Interface\NoteParentEntityInterface;
use App\Entity\Trait\TimestampableAttributeEntityTrait;
use App\Repository\DealNoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DealNoteRepository::class)]
class DealNote extends AbstractNoteEntity
{
    use TimestampableAttributeEntityTrait;

    #[ORM\ManyToOne(targetEntity: Deal::class, inversedBy: 'dealNotes')]
    #[ORM\JoinColumn(nullable: false, referencedColumnName: 'id', onDelete: 'CASCADE')]
    private $deal;

    public function getDeal(): ?Deal
    {
        return $this->deal;
    }

    public function setDeal(?Deal $deal): self
    {
        $this->deal = $deal;

        return $this;
    }

    public function setParent(NoteParentEntityInterface $deal): self
    {
        return $this->setDeal($deal);
    }

    public function getParent(): ?NoteParentEntityInterface
    {
        return $this->getDeal();
    }
}
