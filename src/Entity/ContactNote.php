<?php

namespace App\Entity;

use App\Entity\Abstract\AbstractNoteEntity;
use App\Entity\Interface\NoteParentEntityInterface;
use App\Entity\Trait\TimestampableAttributeEntityTrait;
use App\Repository\ContactNoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactNoteRepository::class)]
class ContactNote extends AbstractNoteEntity
{
    use TimestampableAttributeEntityTrait;

    #[ORM\ManyToOne(targetEntity: Contact::class, inversedBy: 'contactNotes')]
    #[ORM\JoinColumn(nullable: false, referencedColumnName: 'id', onDelete: 'CASCADE')]
    private $contact;


    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    public function setParent($contact): self
    {
        return $this->setContact($contact);
    }

    public function getParent(): ?NoteParentEntityInterface
    {
        return $this->getContact();
    }
}
