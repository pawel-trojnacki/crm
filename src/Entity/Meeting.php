<?php

namespace App\Entity;

use App\Dto\MeetingDto;
use App\Repository\MeetingRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;

#[ORM\Entity(repositoryClass: MeetingRepository::class)]
class Meeting
{
    public const IMORTANCE_HIGH = 'High';
    public const IMPORTANCE_NORMAL = 'Normal';
    public const IMPORTANCE_LOW = 'Low';

    public const IMPORTANCE_OPTIONS = [
        self::IMORTANCE_HIGH,
        self::IMPORTANCE_NORMAL,
        self::IMPORTANCE_LOW,
    ];

    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    private $name;

    /**
     * @Gedmo\Slug(fields={"name"})
     */
    #[ORM\Column(type: 'string', length: 255)]
    private $slug;

    #[ORM\ManyToOne(targetEntity: Workspace::class, inversedBy: 'meetings')]
    #[ORM\JoinColumn(nullable: false, referencedColumnName: 'id', onDelete: 'CASCADE')]
    private $workspace;

    #[ORM\Column(type: 'string', length: 20)]
    private $importance;

    #[ORM\Column(type: 'datetime')]
    private $beginAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $endAt;

    #[ORM\ManyToOne(targetEntity: Contact::class, inversedBy: 'meetings')]
    #[ORM\JoinColumn(nullable: true, referencedColumnName: 'id', onDelete: 'SET NULL')]
    private $contact;

    public function __construct(
        Workspace $workspace,
        string $name,
        string $importance,
        \DateTime $beginAt,
        ?\DateTime $endAt = null,
        ?Contact $contact = null,
    ) {
        $this->id = Uuid::uuid4();
        $this->workspace = $workspace;
        $this->importance = $importance;
        $this->name = $name;
        $this->beginAt = $beginAt;
        $this->endAt = $endAt;
        $this->contact = $contact;
    }

    public static function createFromDto(Workspace $workspace, MeetingDto $dto): self
    {
        return new self($workspace, $dto->name, $dto->importance, $dto->beginAt, $dto->endAt, $dto->contact);
    }

    public function updateFromDto(MeetingDto $dto): self
    {
        $this->name = $dto->name;
        $this->importance = $dto->importance;
        $this->beginAt = $dto->beginAt;
        $this->endAt = $dto->endAt;
        $this->contact = $dto->contact;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): ?string
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

    public function getWorkspace(): ?Workspace
    {
        return $this->workspace;
    }

    public function getImportance(): string
    {
        return $this->importance;
    }

    public function getBeginAt(): ?\DateTimeInterface
    {
        return $this->beginAt;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }
}
