<?php

declare(strict_types=1);

namespace App\Command;

use App\Enum\ConsoleReturnEnum;
use App\Service\Maintenance\MoveFilesToNewStructure\Dto\MoveFilesToNewLocationDto;
use App\Service\Maintenance\MoveFilesToNewStructure\MoveFilesToNewLocationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MoveFilesToNewLocationCommand extends Command
{
    protected static $defaultName = 'app:move-files-new-location';

    /**
     * @var MoveFilesToNewLocationService
     */
    private $moveFilesToNewLocationService;

    public function __construct(MoveFilesToNewLocationService $moveFilesToNewLocationService)
    {
        parent::__construct();
        $this->moveFilesToNewLocationService = $moveFilesToNewLocationService;
    }

    protected function configure(): void
    {
        $this->setDescription('move files to new location');
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'dry run, do not perform move');
        $this->addOption('limit', null, InputOption::VALUE_REQUIRED, 'limit records to update');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $moveFilesToNewLocationDto = new MoveFilesToNewLocationDto();
        if ($input->getOption('limit')) {
            $moveFilesToNewLocationDto->setLimit((int) $input->getOption('limit'));
        }
        if (false === $input->getOption('dry-run')) {
            $moveFilesToNewLocationDto->setPerformMove(true);
        }
        $this->moveFilesToNewLocationService->moveToNewLocation($moveFilesToNewLocationDto);

        $io->success('done');

        return ConsoleReturnEnum::SUCCESS;
    }
}
