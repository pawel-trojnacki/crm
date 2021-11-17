<?php

namespace App\Entity;

use App\Entity\Trait\TimestampableAttributeEntityTrait;
use App\Repository\ContactRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    use TimestampableAttributeEntityTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 30)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 30,
    )]
    private $firstName;

    #[ORM\Column(type: 'string', length: 30)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 30,
    )]
    private $lastName;

    #[ORM\Column(type: 'string', length: 80)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private $email;

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 8,
        max: 20,
    )]
    private $phone;

    /**
     * @Gedmo\Slug(fields={"firstName", "lastName"})
     */
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private $slug;

    #[ORM\Column(type: 'string', length: 30, nullable: true)]
    private $position;

    #[ORM\ManyToOne(targetEntity: Workspace::class, inversedBy: 'contacts')]
    #[ORM\JoinColumn(nullable: false, referencedColumnName: 'id', onDelete: 'CASCADE')]
    private $workspace;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'contacts')]
    #[ORM\JoinColumn(nullable: true, referencedColumnName: 'id', onDelete: 'SET NULL')]
    private $company;

    #[ORM\OneToMany(
        mappedBy: 'contact',
        targetEntity: ContactNote::class,
        cascade: ['persist', 'remove']
    )]
    private $contactNotes;

    public function __construct()
    {
        $this->contactNotes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

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

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): self
    {
        $this->position = $position;

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
     * @return Collection|ContactNote[]
     */
    public function getContactNotes(): Collection
    {
        return $this->contactNotes;
    }

    public function addContactNote(ContactNote $contactNote): self
    {
        if (!$this->contactNotes->contains($contactNote)) {
            $this->contactNotes[] = $contactNote;
            $contactNote->setContact($this);
        }

        return $this;
    }

    public function removeContactNote(ContactNote $contactNote): self
    {
        if ($this->contactNotes->removeElement($contactNote)) {
            // set the owning side to null (unless already changed)
            if ($contactNote->getContact() === $this) {
                $contactNote->setContact(null);
            }
        }

        return $this;
    }
}
