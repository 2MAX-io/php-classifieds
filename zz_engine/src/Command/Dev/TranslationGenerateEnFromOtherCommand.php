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
use Symfony\Component\Yaml\Yaml;

class TranslationGenerateEnFromOtherCommand extends Command
{
    protected static $defaultName = 'app:dev:translation:generate-en-from-other';

    protected function configure(): void
    {
        $this->setDescription('generate en translations from other language');
        $this->addArgument('sourceFile', InputArgument::REQUIRED, 'Source file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $sourceFile = $input->getArgument('sourceFile');

        $translations = Yaml::parseFile($sourceFile);
        $translations = Arr::unpack($translations);

        $translationKeys = \array_keys($translations);
        $translationValues = \array_map(static function (string $element) {
            // use key value after `trans.` as translation value
            return \preg_replace('`^trans.(.*)`', '$1', $element);
        }, $translationKeys);
        $translations = \array_combine($translationKeys, $translationValues);

        \file_put_contents(
            \dirname($sourceFile).'/'.DateHelper::date('Y-m-d H:i:s_').\basename($sourceFile),
            Yaml::dump($translations)
        );

        $io->success('done');

        return ConsoleReturnEnum::SUCCESS;
    }
}
