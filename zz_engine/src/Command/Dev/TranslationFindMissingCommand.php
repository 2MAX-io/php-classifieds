<?php

declare(strict_types=1);

namespace App\Command\Dev;

use App\Enum\ConsoleReturnEnum;
use App\Service\System\Dev\MissingTranslations\FindMissingTranslationsService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TranslationFindMissingCommand extends Command
{
    protected static $defaultName = 'app:dev:translation:find-missing';

    /**
     * @var FindMissingTranslationsService
     */
    private $findMissingTranslationsService;

    public function __construct(FindMissingTranslationsService $findMissingTranslationsService)
    {
        parent::__construct();

        $this->findMissingTranslationsService = $findMissingTranslationsService;
    }

    protected function configure(): void
    {
        $this->setDescription('find missing translations');
        $this->addArgument('sourceFile', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $sourceFile = $input->getArgument('sourceFile');
        $missingTranslations = $this->findMissingTranslationsService->findMissingTranslations($sourceFile);
        $missingTranslationsCount = \count($missingTranslations);

        foreach ($missingTranslations as $missingTranslation) {
            $io->writeln($missingTranslation);
        }

        if ($missingTranslationsCount > 0) {
            $io->error('MISSING TRANSLATIONS FOUND');
        } else {
            $io->success('no missing translations');
        }

        return ConsoleReturnEnum::SUCCESS;
    }
}
