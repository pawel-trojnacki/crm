<?php

namespace App\Dto;

use App\Dto\Abstract\AbstractUserDto;

class RegisterUserDto extends AbstractUserDto
{
    public string $plainPassword;

    public ?string $role = null;
}