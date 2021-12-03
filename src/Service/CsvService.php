<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\Contact;
use Symfony\Component\HttpFoundation\Response;

class CsvService
{
    public function returnCsvResponse(Response $response, string $filename): Response
    {
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set(
            'Content-Disposition',
            sprintf('attachment; filename="%s.csv"', $filename)
        );

        $response->sendHeaders();

        return $response;
    }

    /** @param Contact[] $contacts */
    public function getCsvContacts(array $contacts): string
    {
        $csvData = 'first_name,last_name,email,phone,company,position' . PHP_EOL;

        foreach ($contacts as $contact) {
            $csvData .= implode(',', [
                $contact->getFirstName(),
                $contact->getLastName(),
                $contact->getEmail(),
                $contact->getPhone(),
                $contact->getCompany() ?
                    str_replace(',', ' ', $contact->getCompany()->getName()) : ' ',
                $contact->getPosition() ? $contact->getPosition() : ' ',
            ]) . PHP_EOL;
        }

        return $csvData;
    }

    /** @param Company[] $companies */
    public function getCsvCompanies(array $companies): string
    {
        $csvData = 'name,industry,country,city,website,address' . PHP_EOL;

        foreach ($companies as $company) {
            $csvData .= implode(',', [
                str_replace(',', ' ', $company->getName()),
                $company->getIndustry() ? $company->getIndustry()->getName() : ' ',
                $company->getCountry() ? $company->getCountry()->getName() : ' ',
                $company->getCity(),
                $company->getWebsite(),
                $company->getAddress(),
            ]) . PHP_EOL;
        }

        return $csvData;
    }
}
