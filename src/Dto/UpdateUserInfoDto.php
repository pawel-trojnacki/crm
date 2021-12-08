<?php

namespace App\Dto;

use App\Dto\Abstract\AbstractUserDto;

class UpdateUserInfoDto extends AbstractUserDto
{
    public ?array $roles = ['ROLE_USER'];
}
