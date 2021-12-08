<?php

namespace App\Entity;

use App\Entity\Trait\TimestampableAttributeEntityTrait;
use App\Repository\WorkspaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: WorkspaceRepository::class)]
class Workspace
{
    use TimestampableAttributeEntityTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private $id;

    #[ORM\Column(type: 'string', length: 30)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 6,
        max: 30
    )]
    private $name;

    /**
     * @Gedmo\Slug(fields={"name"})
     */
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private $slug;

    #[ORM\OneToMany(
        mappedBy: 'workspace',
        targetEntity: Contact::class,
        cascade: ['persist', 'remove']
    )]
    private $contacts;

    #[ORM\OneToMany(
        mappedBy: 'workspace',
        targetEntity: Company::class,
        cascade: ['persist', 'remove']
    )]
    private $companies;

    #[ORM\OneToMany(
        mappedBy: 'workspace',
        targetEntity: User::class,
        cascade: ['persist', 'remove']
    )]
    private $users;

    #[ORM\OneToMany(
        mappedBy: 'workspace',
        targetEntity: Deal::class,
        cascade: ['persist', 'remove']
    )]
    private $deals;

    #[ORM\OneToMany(
        mappedBy: 'workspace',
        targetEntity: Meeting::class,
        cascade: ['persist', 'remove']
    )]
    private $meetings;

    public function __construct(string $name)
    {
        $this->id = Uuid::uuid4();
        $this->name = $name;
        $this->contacts = new ArrayCollection();
        $this->companies = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->deals = new ArrayCollection();
        $this->meetings = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function changeName(string $name): self {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Contact[]
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    /**
     * @return Collection|Company[]
     */
    public function getCompanies(): Collection
    {
        return $this->companies;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @return Collection|Deal[]
     */
    public function getDeals(): Collection
    {
        return $this->deals;
    }

    /**
     * @return Collection|Meeting[]
     */
    public function getMeetings(): Collection
    {
        return $this->meetings;
    }
}
