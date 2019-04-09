<?php

declare(strict_types=1);

namespace App\Service\User\Create;

use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Hackzilla\PasswordGenerator\RandomGenerator\Php7RandomGenerator;

class PasswordGenerateService
{
    public function generatePassword(int $length = 15)
    {
        $generator = new ComputerPasswordGenerator();

        $generator
            ->setLength($length)
            ->setRandomGenerator(new Php7RandomGenerator())
            ->setOptionValue(ComputerPasswordGenerator::OPTION_UPPER_CASE, false)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_LOWER_CASE, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_NUMBERS, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_SYMBOLS, false)
            ->setOptionValue(ComputerPasswordGenerator::PARAMETER_SIMILAR, true)
        ;

        return $generator->generatePassword();
    }
}
