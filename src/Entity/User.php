<?php

namespace App\Entity;

use App\Dto\RegisterUserDto;
use App\Dto\UpdatePasswordDto;
use App\Dto\UpdateUserInfoDto;
use App\Entity\Trait\TimestampableAttributeEntityTrait;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_MANAGER = 'ROLE_MANAGER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public const ROLES = [self::ROLE_USER, self::ROLE_MANAGER, self::ROLE_ADMIN];

    use TimestampableAttributeEntityTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Workspace::class, inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false, referencedColumnName: 'id', onDelete: 'CASCADE')]
    private $workspace;

    #[ORM\Column(type: 'string', length: 30)]
    private $firstName;

    #[ORM\Column(type: 'string', length: 30)]
    private $lastName;

    /**
     * @Gedmo\Slug(fields={"firstName", "lastName"})
     */
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private $slug;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    public function __construct(
        Workspace $workspace,
        string $firstName,
        string $lastName,
        string $email,
        ?string $role,
    ) {
        $this->id = Uuid::uuid4();
        $this->workspace = $workspace;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;

        if ($role) {
            $this->addRole($role);
        } else {
            $this->addRole(self::ROLE_USER);
        }
    }

    public function __toString()
    {
        return ucwords($this->firstName . ' ' . $this->lastName);
    }

    public static function createFromRegisterDto(Workspace $workspace, RegisterUserDto $dto): self
    {
        $user = new self($workspace, $dto->firstName, $dto->lastName, $dto->email, $dto->role);

        return $user;
    }

    public function updateFromDto(UpdateUserInfoDto $dto): self
    {
        $this->updatedAt = new \DateTime();
        $this->firstName = $dto->firstName;
        $this->lastName = $dto->lastName;

        if ($dto->role) {
            $this->roles = [$dto->role];
        }

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getWorkspace(): ?Workspace
    {
        return $this->workspace;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(string $role): self
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
