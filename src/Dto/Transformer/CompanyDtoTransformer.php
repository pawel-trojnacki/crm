<?php

namespace App\Dto\Transformer;

use App\Dto\CompanyDto;
use App\Dto\Transformer\Interface\DtoTransformerInterface;
use App\Entity\Company;

class CompanyDtoTransformer implements DtoTransformerInterface
{
    public function transformFromObject(Company $company): CompanyDto
    {
        $companyDto = new CompanyDto();

        $companyDto->name = $company->getName();
        $companyDto->industry = $company->getIndustry();
        $companyDto->website = $company->getWebsite();
        $companyDto->address = $company->getAddress();
        $companyDto->city = $company->getCity();
        $companyDto->country = $company->getCountry();

        return $companyDto;
    }
}
