<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueUser extends Constraint
{
    public string $message = 'User already exists';

    public string $field;

    public function getRequiredOptions(): array
    {
        return ['field'];
    }

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return self::class . 'Validator';
    }
}
