<?php

namespace App\Tests\Helper;

use App\Entity\User;
use App\Entity\Workspace;

class UserTestHelper {
    public const DEFAULTS = [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john-doe@email.com',
    ];

    public static function createDefaultUser(Workspace $workspace): User
    {
        $user = new User();

        $user->setWorkspace($workspace);
        $user->setFirstName(self::DEFAULTS['firstName']);
        $user->setLastName(self::DEFAULTS['lastName']);
        $user->setEmail(self::DEFAULTS['email']);

        return $user;
    }

}