<?php

declare(strict_types=1);

namespace App\Service\User\Account\Secondary;

use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Hackzilla\PasswordGenerator\RandomGenerator\Php7RandomGenerator;

class PasswordGenerateService
{
    public function generatePassword(int $length = 15): string
    {
        $generator = new ComputerPasswordGenerator();
        $generator->setLength($length);
        $generator->setRandomGenerator(new Php7RandomGenerator());
        $generator->setOptionValue(ComputerPasswordGenerator::OPTION_UPPER_CASE, false);
        $generator->setOptionValue(ComputerPasswordGenerator::OPTION_LOWER_CASE, true);
        $generator->setOptionValue(ComputerPasswordGenerator::OPTION_NUMBERS, true);
        $generator->setOptionValue(ComputerPasswordGenerator::OPTION_SYMBOLS, false);
        $generator->setOptionValue(ComputerPasswordGenerator::PARAMETER_SIMILAR, true);

        return $generator->generatePassword();
    }
}
