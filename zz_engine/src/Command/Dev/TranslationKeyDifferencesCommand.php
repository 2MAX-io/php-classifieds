<?php

declare(strict_types=1);

namespace App\Command\Dev;

use App\Enum\ConsoleReturnEnum;
use Minwork\Helper\Arr;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;

class TranslationKeyDifferencesCommand extends Command
{
    protected static $defaultName = 'app:dev:translation:missing';

    protected function configure(): void
    {
        $this->setDescription('find missing translation key between files');
        $this->addArgument('sourceFileA', InputArgument::REQUIRED);
        $this->addArgument('sourceFileB', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $sourceFileA = $input->getArgument('sourceFileA');
        $sourceFileB = $input->getArgument('sourceFileB');

        $translationsA = Yaml::parseFile($sourceFileA);
        $translationsA = Arr::unpack($translationsA);
        $translationsA = \array_keys($translationsA);

        $translationsB = Yaml::parseFile($sourceFileB);
        $translationsB = Arr::unpack($translationsB);
        $translationsB = \array_keys($translationsB);

        $noDifferences = true;
        foreach (\array_diff($translationsB, $translationsA) as $missingTranslationKey) {
            $noDifferences = false;
            $io->writeln($sourceFileA.': '.$missingTranslationKey);
        }

        foreach (\array_diff($translationsA, $translationsB) as $missingTranslationKey) {
            $noDifferences = false;
            $io->writeln($sourceFileB.': '.$missingTranslationKey);
        }

        if ($noDifferences) {
            $io->success('no differences');
        } else {
            $io->error('differences found');
        }

        return ConsoleReturnEnum::SUCCESS;
    }
}
