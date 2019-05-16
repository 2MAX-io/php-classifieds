<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\RegexValidator;

class Value extends Regex
{
    public $message = 'Contains not allowed characters. Allowed characters: plain letters, numbers, minus character, underscore character';
    public $pattern = '#^[a-zA-Z0-9\-_]*$#';

    public function getRequiredOptions(): array
    {
        return [];
    }

    public function validatedBy(): string
    {
        return RegexValidator::class;
    }
}
