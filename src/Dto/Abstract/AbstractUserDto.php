<?php

namespace App\Dto\Abstract;

use App\Entity\User;
use App\Validator\UniqueField;
use Symfony\Component\Validator\Constraints as Assert;

class AbstractUserDto
{
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 30,
    )]
    public string $firstName;

    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 30,
    )]
    public string $lastName;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[UniqueField(['entityClass' => User::class, 'field' => 'email'])]
    public string $email;
}
