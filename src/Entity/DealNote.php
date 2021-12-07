<?php

namespace App\Entity;

use App\Dto\NoteDto;
use App\Entity\Abstract\AbstractNoteEntity;
use App\Entity\Interface\NoteParentEntityInterface;
use App\Repository\DealNoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DealNoteRepository::class)]
class DealNote extends AbstractNoteEntity
{
    #[ORM\ManyToOne(targetEntity: Deal::class, inversedBy: 'notes')]
    #[ORM\JoinColumn(nullable: false, referencedColumnName: 'id', onDelete: 'CASCADE')]
    private $parent;

    public function __construct(Deal $parent, User $creator, string $content)
    {
        parent::__construct($creator, $content);
        $this->parent = $parent;
    }

    public function getType()
    {
        return 'DealNote';
    }

    public static function createFromDto(Deal $parent, User $creator, NoteDto $dto): self
    {
        return new self($parent, $creator, $dto->content);
    }

    public function updateFromDto(NoteDto $dto): self
    {
        $this->content = $dto->content;

        return $this;
    }

    public function getParent(): NoteParentEntityInterface
    {
        return $this->parent;
    }
}
