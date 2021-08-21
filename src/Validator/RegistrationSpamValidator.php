<?php

namespace App\Validator;

use App\Homework\RegistrationSpamFilter;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RegistrationSpamValidator extends ConstraintValidator
{
    public function __construct(private RegistrationSpamFilter $registrationSpamFilter)
    {
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint RegistrationSpam */

        if (null === $value || '' === $value) {
            return;
        }


        if ($this->registrationSpamFilter->filter($value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
