<?php

namespace App\Entity;

use App\Dto\DealDto;
use App\Entity\Interface\NoteParentEntityInterface;
use App\Entity\Trait\TimestampableAttributeEntityTrait;
use App\Repository\DealRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;

#[ORM\Entity(repositoryClass: DealRepository::class)]
class Deal implements NoteParentEntityInterface
{
    public const ACTIVE_STAGES = ['opportunity', 'proposal sent', 'in negociation'];
    public const STAGES = [...self::ACTIVE_STAGES, 'won', 'lost'];

    use TimestampableAttributeEntityTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Workspace::class, inversedBy: 'deals')]
    #[ORM\JoinColumn(nullable: false, referencedColumnName: 'id', onDelete: 'CASCADE')]
    private $workspace;

    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: true, referencedColumnName: 'id', onDelete: 'SET NULL')]
    private $creator;

    #[ORM\Column(type: 'string', length: 80)]
    private $name;

    /**
     * @Gedmo\Slug(fields={"name"})
     */
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private $slug;

    #[ORM\Column(type: 'string', length: 25)]
    private $stage;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'deals', fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false, referencedColumnName: 'id', onDelete: 'CASCADE')]
    private $company;

    #[ORM\ManyToMany(targetEntity: User::class)]
    private $users;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\OneToMany(
        mappedBy: 'parent',
        targetEntity: DealNote::class,
        cascade: ['persist', 'remove']
    )]
    private $notes;

    public function __construct(
        Workspace $workspace,
        User $creator,
        string $name,
        string $stage,
        Company $company,
        Collection|array $users = null,
        ?string $description = null,
    ) {
        $this->id = Uuid::uuid4();
        $this->workspace = $workspace;
        $this->creator = $creator;
        $this->name = $name;
        $this->stage = $stage;
        $this->company = $company;
        $this->description = $description;

        if ($users) {
            $this->users = $users;
        } else {
            $this->users = new ArrayCollection();
        }

        $this->dealNotes = new ArrayCollection();
    }

    public static function createFromDto(Workspace $workspace, User $creator, DealDto $dto): self
    {
        return new self($workspace, $creator, $dto->name, $dto->stage, $dto->company, $dto->users, $dto->description);
    }

    public function updateFromDto(DealDto $dto): self
    {
        $this->name = $dto->name;
        $this->stage = $dto->stage;
        $this->company = $dto->company;
        $this->users = $dto->users;
        $this->description = $dto->description;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
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

    public function getStage(): string
    {
        return $this->stage;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function getWorkspace(): Workspace
    {
        return $this->workspace;
    }

    /**
     * @return Collection|DealNote[]
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }
}
