<?php

namespace App\Dto;

use App\Dto\Abstract\AbstractUserDto;

class RegisterUserDto extends AbstractUserDto
{
    public string $plainPassword;

    public ?array $roles = ['ROLE_USER'];
}
