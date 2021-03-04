<?php

declare(strict_types=1);

namespace App\Command\Dev;

use App\Enum\ConsoleReturnEnum;
use App\Helper\DateHelper;
use Minwork\Helper\Arr;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Translation\Dumper\XliffFileDumper;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Yaml\Yaml;

class TranslationConvertYamlToXliffCommand extends Command
{
    protected static $defaultName = 'app:dev:translation:yaml-to-xliff';

    protected function configure(): void
    {
        $this
            ->setDescription('convert yaml translation to xliff')
            ->addArgument('sourceFile', InputArgument::OPTIONAL, 'Source file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $sourceFile = $input->getArgument('sourceFile');

        $translations = Yaml::parseFile($sourceFile);
        $translations = Arr::unpack($translations);

        $xliffFileDumper = new XliffFileDumper();
        $messageCatalogue = new MessageCatalogue('en');
        $messageCatalogue->add($translations);

        $fileName = \pathinfo($sourceFile, \PATHINFO_FILENAME);
        $filePath = \dirname($sourceFile)
            .'/'.DateHelper::date('Y-m-d H:i:s_')
            .\basename($fileName)
            .'.xliff'
        ;
        \file_put_contents(
            $filePath,
            $xliffFileDumper->formatCatalogue($messageCatalogue, 'messages')
        );

        $io->success('done');

        return ConsoleReturnEnum::SUCCESS;
    }
}
