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
use Symfony\Component\Yaml\Yaml;

class TranslationGenerateToEnCommand extends Command
{
    protected static $defaultName = 'app:translation:generate-to-en';

    protected function configure()
    {
        $this
            ->setDescription('Generate base, en translation from other language')
            ->addArgument('sourceFile', InputArgument::OPTIONAL, 'Source file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $sourceFile = $input->getArgument('sourceFile');

        $translations = Yaml::parseFile(FilePath::getFile($sourceFile));
        $translations = Arr::unpack($translations);

        $translationKeys = \array_keys($translations);
        $translationValues = \array_map(function(string $element) {
            return \preg_replace('`^trans.(.*)`', '$1', $element);
        }, $translationKeys);

        $translations = \array_combine($translationKeys, $translationValues);

        \file_put_contents(
            \dirname(FilePath::getFile($sourceFile)) . '/' . date('Y-m-d H:i:s_') . \basename(FilePath::getFile($sourceFile)),
            Yaml::dump($translations)
        );

        $io->success('done');
    }
}
