<?php

namespace App\Tests\Helper;

use App\Entity\Contact;
use App\Entity\Workspace;

class ContactTestHelper
{
    public const DEFAULTS = [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'johndoe@email.com',
        'phone' => '541-814-1739',
        'position' => 'manager',
    ];

    public static function createDefaultContact(Workspace $workspace): Contact
    {
        $contact = new Contact();

        $contact->setFirstName(self::DEFAULTS['firstName']);
        $contact->setLastName(self::DEFAULTS['lastName']);
        $contact->setEmail(self::DEFAULTS['email']);
        $contact->setPhone(self::DEFAULTS['phone']);
        $contact->setPosition(self::DEFAULTS['position']);
        $contact->setWorkspace($workspace);

        return $contact;
    }
}
