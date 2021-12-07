<?php

namespace App\Dto;

use App\Entity\Company;
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

    public static function createFromCompany(Company $company): self
    {
        $companyDto = new self();

        $companyDto->name = $company->getName();
        $companyDto->industry = $company->getIndustry();
        $companyDto->website = $company->getWebsite();
        $companyDto->address = $company->getAddress();
        $companyDto->city = $company->getCity();
        $companyDto->country = $company->getCountry();

        return $companyDto;
    }
}
