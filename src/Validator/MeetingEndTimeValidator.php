<?php

namespace App\Validator;

use App\Dto\MeetingDto;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class MeetingEndTimeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof MeetingDto) {
            throw new UnexpectedValueException($value, MeetingDto::class);
        }

        if (!$constraint instanceof MeetingEndTime) {
            throw new UnexpectedTypeException($constraint, MeetingEndTime::class);
        }

        /** @var MeetingDto $dto */
        $dto = $value;

        if ($dto->endAt && $dto->endAt < $dto->beginAt) {
            $this->context->buildViolation($constraint->message)
                ->atPath('endAt')
                ->addViolation();
        }
    }
}
