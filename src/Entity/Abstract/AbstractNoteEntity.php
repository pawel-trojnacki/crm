<?php

namespace App\Entity\Abstract;

use App\Entity\Interface\NoteParentEntityInterface;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

abstract class AbstractNoteEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    protected $id;

    #[ORM\Column(type: 'datetime')]
    protected \DateTime $createdAt;

    #[ORM\Column(type: 'datetime')]
    protected \DateTime $updatedAt;

    #[ORM\Column(type: 'text')]
    protected $content;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, referencedColumnName: 'id', onDelete: 'SET NULL')]
    protected $creator;

    public function __construct(User $creator, string $content)
    {
        $this->id = Uuid::uuid4();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->creator = $creator;
        $this->content = $content;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    abstract function getParent(): ?NoteParentEntityInterface;
}
