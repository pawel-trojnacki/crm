<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class MeetingEndTime extends Constraint
{
    public string $message = 'End date should be greater than begin date';

    public function getRequiredOptions(): array
    {
        return [];
    }
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return self::class . 'Validator';
    }
}
