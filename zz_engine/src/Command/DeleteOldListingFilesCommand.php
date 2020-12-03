<?php

declare(strict_types=1);

namespace App\Command;

use App\Enum\ConsoleReturnEnum;
use App\Service\Cron\DeleteOldListingFiles\Dto\DeleteOldListingFilesDto;
use App\Service\Cron\DeleteOldListingFiles\DeleteOldListingFilesService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeleteOldListingFilesCommand extends Command
{
    protected static $defaultName = 'app:delete-old-listing-files';

    /**
     * @var DeleteOldListingFilesService
     */
    private $removeOldListingFilesService;

    public function __construct(DeleteOldListingFilesService $removeOldListingFilesService)
    {
        parent::__construct();
        $this->removeOldListingFilesService = $removeOldListingFilesService;
    }

    protected function configure(): void
    {
        $this->setDescription('delete old listing files');
        $this->addArgument('days', InputArgument::REQUIRED, 'delete listing files older than given number of days');
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'dry run, do not perform delete');
        $this->addOption('limit', null, InputOption::VALUE_REQUIRED, 'limit of records to update');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $days = (int) $input->getArgument('days');

        $removeOldListingFilesDto = new DeleteOldListingFilesDto();
        $removeOldListingFilesDto->setDeleteOlderThanInDays($days);
        if ($input->getOption('limit')) {
            $removeOldListingFilesDto->setLimit((int) $input->getOption('limit'));
        }
        if (false === $input->getOption('dry-run')) {
            $removeOldListingFilesDto->setPerformFileDeletion(true);
        }
        $this->removeOldListingFilesService->removeListingFilesOlderThan($removeOldListingFilesDto);

        $io->success('done');

        return ConsoleReturnEnum::SUCCESS;
    }
}
