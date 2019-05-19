<?php

declare(strict_types=1);

namespace App\Command;

use App\Helper\FilePath;
use Minwork\Helper\Arr;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Translation\Dumper\XliffFileDumper;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Yaml\Yaml;

class TranslationConvertCommand extends Command
{
    protected static $defaultName = 'app:translation:convert';

    protected function configure()
    {
        $this
            ->setDescription('translation convert')
            ->addArgument('sourceFile', InputArgument::OPTIONAL, 'Source file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $sourceFile = $input->getArgument('sourceFile');

        $translations = Yaml::parseFile(FilePath::getFile($sourceFile));
        $translations = Arr::unpack($translations);

        $xliffFileDumper = new XliffFileDumper();
        $messageCatalogue = new MessageCatalogue('pl');
        $messageCatalogue->add($translations, 'messages');
        $output = $xliffFileDumper->formatCatalogue($messageCatalogue, 'messages');

        \file_put_contents(
            \dirname(FilePath::getFile($sourceFile)) . '/' . date('Y-m-d H:i:s_') . \basename(FilePath::getFile($sourceFile)),
            $output
        );

        $io->success('done');
    }
}
