<?php

namespace App\Validator;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueUserValidator extends ConstraintValidator
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $userRepository,
    ) {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!is_scalar($constraint->field)) {
            throw new \InvalidArgumentException('"field" parameter should be any scalar type');
        }

        $searchResults = $this->userRepository->findBy([
            $constraint->field => $value
        ]);

        if (count($searchResults) > 0) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
