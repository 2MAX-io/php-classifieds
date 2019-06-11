<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class HasLetterNumber extends Constraint
{
    public $message = 'Must contain letters or numbers';

    public function validatedBy(): string
    {
        return HasLetterNumberValidator::class;
    }
}
