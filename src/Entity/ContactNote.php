<?php

namespace App\Entity;

use App\Dto\NoteDto;
use App\Entity\Abstract\AbstractNoteEntity;
use App\Entity\Interface\NoteParentEntityInterface;
use App\Repository\ContactNoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactNoteRepository::class)]
class ContactNote extends AbstractNoteEntity
{
    public const NOTE_TYPE = 'CONTACT_NOTE';

    #[ORM\ManyToOne(targetEntity: Contact::class, inversedBy: 'notes')]
    #[ORM\JoinColumn(nullable: false, referencedColumnName: 'id', onDelete: 'CASCADE')]
    protected NoteParentEntityInterface $parent;

    public function __construct(Contact $parent, User $creator, string $content)
    {
        parent::__construct($creator, $content);
        $this->parent = $parent;
    }

    public static function createFromDto(Contact $parent, User $creator, NoteDto $dto): self
    {
        return new self($parent, $creator, $dto->content);
    }

    public function updateFromDto(NoteDto $dto): self
    {
        $this->content = $dto->content;

        return $this;
    }
}
