<?php

namespace App\Entity;

use App\Dto\ContactDto;
use App\Entity\Interface\NoteParentEntityInterface;
use App\Repository\ContactRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact implements NoteParentEntityInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: Workspace::class, inversedBy: 'contacts')]
    #[ORM\JoinColumn(nullable: false, referencedColumnName: 'id', onDelete: 'CASCADE')]
    private $workspace;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, referencedColumnName: 'id', onDelete: 'SET NULL')]
    private $creator;

    #[ORM\Column(type: 'string', length: 30)]
    private $firstName;

    #[ORM\Column(type: 'string', length: 30)]
    private $lastName;

    /**
     * @Gedmo\Slug(fields={"firstName", "lastName"})
     */
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private $slug;

    #[ORM\Column(type: 'string', length: 80)]
    private $email;

    #[ORM\Column(type: 'string', length: 20)]
    private $phone;

    #[ORM\Column(type: 'string', length: 30, nullable: true)]
    private $position;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'contacts', fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: true, referencedColumnName: 'id', onDelete: 'SET NULL')]
    private $company;

    #[ORM\OneToMany(
        mappedBy: 'parent',
        targetEntity: ContactNote::class,
        cascade: ['persist', 'remove']
    )]
    private $notes;

    public function __construct(
        Workspace $workspace,
        User $creator,
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        ?string $position,
        ?Company $company,
    ) {
        $this->id = Uuid::uuid4();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->workspace = $workspace;
        $this->creator = $creator;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->phone = $phone;
        $this->position = $position;
        $this->company = $company;
        $this->contactNotes = new ArrayCollection();
    }

    public static function createFromDto(Workspace $workspace, User $creator, ContactDto $dto): self
    {
        return new self(
            $workspace,
            $creator,
            $dto->firstName,
            $dto->lastName,
            $dto->email,
            $dto->phone,
            $dto->position,
            $dto->company,
        );
    }

    public function updateFromDto(ContactDto $dto): self
    {
        $this->updatedAt = new \DateTime();
        $this->firstName = $dto->firstName;
        $this->lastName = $dto->lastName;
        $this->phone = $dto->phone;
        $this->email = $dto->email;
        $this->position = $dto->position;
        $this->company = $dto->company;

        return $this;
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

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function getWorkspace(): Workspace
    {
        return $this->workspace;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    /**
     * @return Collection|ContactNote[]
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function getCreator(): User
    {
        return $this->creator;
    }
}
