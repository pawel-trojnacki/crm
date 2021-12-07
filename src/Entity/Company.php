<?php

namespace App\Entity;

use App\Dto\CompanyDto;
use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $updatedAt;

    #[ORM\ManyToOne(targetEntity: Workspace::class, inversedBy: 'companies')]
    #[ORM\JoinColumn(nullable: false, referencedColumnName: 'id', onDelete: 'CASCADE')]
    private $workspace;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, referencedColumnName: 'id', onDelete: 'SET NULL')]
    private $creator;

    #[ORM\Column(type: 'string', length: 80)]
    private $name;

    /**
     * @Gedmo\Slug(fields={"name"})
     */
    #[ORM\Column(type: 'string', length: 255)]
    private $slug;

    #[ORM\ManyToOne(targetEntity: Industry::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: true, referencedColumnName: 'id', onDelete: 'SET NULL')]
    private $industry;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $website;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $address;

    #[ORM\Column(type: 'string', length: 80, nullable: true)]
    private $city;

    #[ORM\ManyToOne(targetEntity: Country::class)]
    private $country;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Contact::class)]
    private $contacts;

    #[ORM\OneToMany(
        mappedBy: 'company',
        targetEntity: Deal::class,
        cascade: ['persist', 'remove']
    )]
    private $deals;

    public function __construct(
        Workspace $workspace,
        User $creator,
        string $name,
        ?Industry $industry = null,
        ?string $website = null,
        ?string $address = null,
        ?string $city = null,
        ?Country $country = null,
    ) {
        $this->id = Uuid::uuid4();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->workspace = $workspace;
        $this->creator = $creator;
        $this->name = $name;
        $this->industry = $industry;
        $this->website = $website;
        $this->address = $address;
        $this->city = $city;
        $this->country = $country;
        $this->contacts = new ArrayCollection();
        $this->deals = new ArrayCollection();
    }

    public static function createFromDto(Workspace $workspace, User $user, CompanyDto $dto): self
    {
        return new self(
            $workspace,
            $user,
            $dto->name,
            $dto->industry,
            $dto->website,
            $dto->address,
            $dto->city,
            $dto->country,
        );
    }

    public function updateFromDto(CompanyDto $dto): self
    {
        $this->updatedAt = new \DateTime();
        $this->name = $dto->name;
        $this->industry = $dto->industry;
        $this->website = $dto->website;
        $this->address = $dto->address;
        $this->city = $dto->city;
        $this->country = $dto->country;

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

    public function getName(): string
    {
        return $this->name;
    }

    public function changeName(string $name): self
    {
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

    public function getIndustry(): ?Industry
    {
        return $this->industry;
    }

    public function getWorkspace(): ?Workspace
    {
        return $this->workspace;
    }

    /**
     * @return Collection|Contact[]
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }


    public function getAddress(): ?string
    {
        return $this->address;
    }


    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    /**
     * @return Collection|Deal[]
     */
    public function getDeals(): Collection
    {
        return $this->deals;
    }
}
