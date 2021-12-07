<?php

namespace App\Dto;

use App\Entity\Company;
use Symfony\Component\Validator\Constraints as Assert;

class ContactDto
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
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Length(
        min: 8,
        max: 20,
    )]
    public string $phone;

    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3,
        max: 30,
    )]
    public ?string $position = null;

    public ?Company $company = null;
}
