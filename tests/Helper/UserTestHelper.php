<?php

namespace App\Tests\Helper;

use App\Entity\User;
use App\Entity\Workspace;

class UserTestHelper
{
    public const DEFAULTS = [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john-doe@email.com',
    ];

    public static function createDefaultUser(Workspace $workspace): User
    {
        $user = new User(
            $workspace,
            self::DEFAULTS['firstName'],
            self::DEFAULTS['lastName'],
            self::DEFAULTS['email'],
            User::ROLE_USER,
        );

        return $user;
    }
}
