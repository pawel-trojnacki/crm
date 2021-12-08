<?php

namespace App\Dto\Transformer;

use App\Dto\Transformer\Interface\DtoTransformerInterface;
use App\Dto\UpdateUserInfoDto;
use App\Entity\User;

class UpdateUserInfoDtoTransformer implements DtoTransformerInterface {
    public function transformFromObject(User $user) {
        $dto = new UpdateUserInfoDto();

        $dto->firstName = $user->getFirstName();
        $dto->lastName = $user->getLastName();
        $dto->email = $user->getEmail();
        $dto->roles = $user->getRoles();

        return $dto;
    }
}