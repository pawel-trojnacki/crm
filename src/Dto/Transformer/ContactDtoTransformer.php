<?php

namespace App\Dto\Transformer;

use App\Dto\ContactDto;
use App\Dto\Transformer\Interface\DtoTransformerInterface;
use App\Entity\Contact;

class ContactDtoTransformer implements DtoTransformerInterface
{
    public function transformFromObject(Contact $contact): ContactDto
    {
        $contactDto = new ContactDto();

        $contactDto->firstName = $contact->getFirstName();
        $contactDto->lastName = $contact->getLastName();
        $contactDto->email = $contact->getEmail();
        $contactDto->phone = $contact->getPhone();
        $contactDto->position = $contact->getPosition();
        $contactDto->company = $contact->getCompany();

        return $contactDto;
    }
}
