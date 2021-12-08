<?php

namespace App\Dto\Transformer;

use App\Dto\MeetingDto;
use App\Dto\Transformer\Interface\DtoTransformerInterface;
use App\Entity\Meeting;

class MeetingDtoTransformer implements DtoTransformerInterface
{
    public function transformFromObject(Meeting $meeting): MeetingDto
    {
        $dto = new MeetingDto();

        $dto->name = $meeting->getName();
        $dto->importance = $meeting->getImportance();
        $dto->beginAt = $meeting->getBeginAt();
        $dto->endAt = $meeting->getEndAt();
        $dto->contact = $meeting->getContact();

        return $dto;
    }
}
