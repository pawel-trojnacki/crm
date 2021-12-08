<?php

namespace App\Dto\Transformer;

use App\Dto\DealDto;
use App\Dto\Transformer\Interface\DtoTransformerInterface;
use App\Entity\Deal;

class DealDtoTransformer implements DtoTransformerInterface {
    public function transformFromObject(Deal $deal): DealDto {
        $dto = new DealDto();

        $dto->name = $deal->getName();
        $dto->stage = $deal->getStage();
        $dto->company = $deal->getCompany();
        $dto->users = $deal->getUsers();
        $dto->description = $deal->getDescription();

        return $dto;
    }
}