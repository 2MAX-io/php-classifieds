<?php

declare(strict_types=1);

namespace App\Command\Dev;

use App\Enum\ParamEnum;
use App\Helper\FilePath;
use App\Service\Dev\GenerateTestData\DevGenerateTestListings;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DevGenerateParamEnumForJsCommand extends Command
{
    protected static $defaultName = 'app:dev:generate-param-enum-for-js';

    /**
     * @var DevGenerateTestListings
     */
    private $devGenerateTestListings;

    public function __construct(DevGenerateTestListings $devGenerateTestListings)
    {
        parent::__construct();

        $this->devGenerateTestListings = $devGenerateTestListings;
    }

    protected function configure(): void
    {
        $this->setDescription('generate param enum for js');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        $fileContent = <<<EOF
"use strict";


EOF;

        $reflectionOfParamEnum = new ReflectionClass(ParamEnum::class);
        foreach ($reflectionOfParamEnum->getConstants() as $constantName => $constantValue) {
            $fileContent .= "app.ParamEnum.{$constantName} = '{$constantValue}';\n";
        }

        file_put_contents(FilePath::getPublicDir().'/asset/enum/ParamEnum.js', $fileContent);

        $io->success('done');
    }
}
