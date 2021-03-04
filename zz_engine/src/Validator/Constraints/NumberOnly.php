<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\RegexValidator;

class NumberOnly extends Regex
{
    /** @var string */
    public $message = 'This value should contain only numbers';

    /** @var string */
    public $pattern = '~^[Z0-9\-]*$~';

    public function getRequiredOptions(): array
    {
        return [];
    }

    public function validatedBy(): string
    {
        return RegexValidator::class;
    }
}
