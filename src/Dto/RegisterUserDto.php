<?php

namespace App\Dto;

use App\Dto\Abstract\AbstractUserDto;
use App\Entity\User;
use App\Validator\UniqueUser;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserDto extends AbstractUserDto
{
    #[Assert\NotBlank]
    #[Assert\Email]
    #[UniqueUser(['field' => 'email'])]
    public string $email;

    public string $plainPassword;

    #[Assert\Choice(choices: User::ROLES)]
    public ?string $role = null;
}
