<?php

namespace App\Tests\Helper;

use App\Entity\Company;
use App\Entity\Contact;
use App\Entity\User;
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

    public static function createDefaultContact(
        Workspace $workspace,
        User $creator,
        ?Company $company = null
    ): Contact {
        $contact = new Contact(
            $workspace,
            $creator,
            self::DEFAULTS['firstName'],
            self::DEFAULTS['lastName'],
            self::DEFAULTS['email'],
            self::DEFAULTS['phone'],
            self::DEFAULTS['position'],
            $company,
        );

        return $contact;
    }
}
