<?php

declare(strict_types=1);

namespace App\Command\Dev;

use App\Enum\ConsoleReturnEnum;
use App\Enum\ParamEnum;
use App\Helper\FilePath;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DevGenerateParamEnumForJsCommand extends Command
{
    protected static $defaultName = 'app:dev:generate-paramEnum-for-js';

    protected function configure(): void
    {
        $this->setDescription('generate param enum for js');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $paramsString = '';
        $paramEnumReflection = new ReflectionClass(ParamEnum::class);
        foreach ($paramEnumReflection->getConstants() as $constantName => $constantValue) {
            $paramsString .= <<<EOF
ParamEnum.{$constantName} = "{$constantValue}";\n
EOF;
        }

        $paramsString = \rtrim($paramsString, "\n");

        $paramEnumFile = <<<EOF
"use strict";

var ParamEnum = {};
{$paramsString}

export { ParamEnum as default };

EOF;

        \file_put_contents(FilePath::getProjectDir().'/zz_engine/assets/enum/ParamEnum.js', $paramEnumFile);

        $io->success('done');

        return ConsoleReturnEnum::SUCCESS;
    }
}
