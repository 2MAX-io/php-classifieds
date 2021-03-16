<?php

declare(strict_types=1);

namespace App\Command\Maintenance;

use App\Enum\ConsoleReturnEnum;
use App\Service\System\Maintenance\DeleteOldListingFiles\DeleteExpiredListingFilesService;
use App\Service\System\Maintenance\DeleteOldListingFiles\Dto\DeleteExpiredListingFilesDto;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeleteExpiredListingFilesCommand extends Command
{
    protected static $defaultName = 'app:delete-expired-listing-files';

    /**
     * @var DeleteExpiredListingFilesService
     */
    private $deleteExpiredListingFilesService;

    public function __construct(
        DeleteExpiredListingFilesService $deleteExpiredListingFilesService
    ) {
        parent::__construct();

        $this->deleteExpiredListingFilesService = $deleteExpiredListingFilesService;
    }

    protected function configure(): void
    {
        $this->setDescription('delete expired listing files');
        $this->addArgument(
            'days',
            InputArgument::OPTIONAL,
            'delete listing files older than given number of days',
        );
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'dry run, do not perform delete');
        $this->addOption('limit', null, InputOption::VALUE_REQUIRED, 'limit of records to update');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $days = (int) $input->getArgument('days');

        $deleteExpiredListingFilesDto = new DeleteExpiredListingFilesDto();
        $deleteExpiredListingFilesDto->setDaysOldToDelete($days);
        if ($input->getOption('limit')) {
            $deleteExpiredListingFilesDto->setLimit((int) $input->getOption('limit'));
        }
        if (false === $input->getOption('dry-run')) {
            $deleteExpiredListingFilesDto->setPerformFileDeletion(true);
        }
        $this->deleteExpiredListingFilesService->deleteExpiredListingFiles($deleteExpiredListingFilesDto);

        $io->success('done');

        return ConsoleReturnEnum::SUCCESS;
    }
}
