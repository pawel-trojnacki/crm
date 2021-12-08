<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueField extends Constraint {
    public string $message = 'This value is already used';

    public string $entityClass;

    public string $field;

    public function getRequiredOptions(): array
    {
        return ['entityClass', 'field'];
    }
    
    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return self::class.'Validator';
    }

}