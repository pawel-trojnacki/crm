<?php

namespace App\Dto;

use App\Entity\Country;
use App\Entity\Industry;
use Symfony\Component\Validator\Constraints as Assert;

class CompanyDto
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 80)]
    public string $name;

    public ?Industry $industry = null;

    #[Assert\Url(relativeProtocol: true)]
    public ?string $website = null;

    #[Assert\Length(min: 3, max: 255)]
    public ?string  $address = null;

    #[Assert\Length(min: 3, max: 80)]
    public ?string $city = null;

    public ?Country $country = null;
}
