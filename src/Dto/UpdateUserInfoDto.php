<?php

namespace App\Dto;

use App\Dto\Abstract\AbstractUserDto;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;


class UpdateUserInfoDto extends AbstractUserDto
{
    public ?array $roles = ['ROLE_USER'];

    #[Assert\Choice(choices: User::ROLES)]
    public ?string $role = null;
}
