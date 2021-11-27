<?php

namespace App\Entity;

use App\Entity\Trait\NoteEntityTrait;
use App\Entity\Trait\TimestampableAttributeEntityTrait;
use App\Repository\ContactNoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactNoteRepository::class)]
class ContactNote
{
    use TimestampableAttributeEntityTrait, NoteEntityTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Contact::class, inversedBy: 'contactNotes')]
    #[ORM\JoinColumn(nullable: false, referencedColumnName: 'id', onDelete: 'CASCADE')]
    private $contact;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): self
    {
        $this->contact = $contact;

        return $this;
    }
}
