<?php

namespace App\Dto;

use App\Entity\Company;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

class DealDto {
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 5,
        max: 80,
    )]
    public string $name;

    #[Assert\NotBlank]
    public string $stage;

    #[Assert\NotBlank]
    public Company $company;

    public ?Collection $users = null;

    #[Assert\Length(
        min: 10,
        max: 1000,
    )]
    public ?string $description = null;
}