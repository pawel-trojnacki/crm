<?php

namespace App\Entity;

use App\Entity\Interface\NoteParentEntityInterface;
use App\Entity\Trait\TimestampableAttributeEntityTrait;
use App\Repository\DealRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DealRepository::class)]
class Deal implements NoteParentEntityInterface
{
    public const ACTIVE_STAGES = ['opportunity', 'proposal sent', 'in negociation'];
    public const STAGES = [...self::ACTIVE_STAGES, 'won', 'lost'];

    use TimestampableAttributeEntityTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 80)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 5,
        max: 80,
    )]
    private $name;

    /**
     * @Gedmo\Slug(fields={"name"})
     */
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private $slug;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(
        min: 10,
        max: 1000,
    )]
    private $description;

    #[ORM\Column(type: 'string', length: 25)]
    #[Assert\NotBlank]
    private $stage;

    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: true, referencedColumnName: 'id', onDelete: 'SET NULL')]
    private $creator;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'deals', fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false, referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[Assert\NotBlank]
    private $company;

    #[ORM\ManyToMany(targetEntity: User::class)]
    private $users;

    #[ORM\ManyToOne(targetEntity: Workspace::class, inversedBy: 'deals')]
    #[ORM\JoinColumn(nullable: false, referencedColumnName: 'id', onDelete: 'CASCADE')]
    private $workspace;

    #[ORM\OneToMany(
        mappedBy: 'deal',
        targetEntity: DealNote::class,
        cascade: ['persist', 'remove']
    )]
    private $dealNotes;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->dealNotes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStage(): ?string
    {
        return $this->stage;
    }

    public function setStage(string $stage): self
    {
        $this->stage = $stage;

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }

    public function getWorkspace(): ?Workspace
    {
        return $this->workspace;
    }

    public function setWorkspace(?Workspace $workspace): self
    {
        $this->workspace = $workspace;

        return $this;
    }

    /**
     * @return Collection|DealNote[]
     */
    public function getNotes(): Collection
    {
        return $this->dealNotes;
    }
}
