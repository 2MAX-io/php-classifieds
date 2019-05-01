<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class Phone extends Constraint
{
    public $message = 'Phone number is incorrect.';

    public function validatedBy(): string
    {
        return PhoneValidator::class;
    }
}
