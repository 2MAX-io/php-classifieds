<?php

declare(strict_types=1);

namespace App\Command\Maintenance;

use App\Enum\ConsoleReturnEnum;
use App\Service\System\Maintenance\RegenerateSearchText\Dto\RegenerateSearchTextDto;
use App\Service\System\Maintenance\RegenerateSearchText\RegenerateSearchTextService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SearchTextRegenerateCommand extends Command
{
    protected static $defaultName = 'app:search-text-regenerate';

    /**
     * @var RegenerateSearchTextService
     */
    private $regenerateSearchTextCron;

    public function __construct(RegenerateSearchTextService $regenerateSearchTextCron)
    {
        parent::__construct();

        $this->regenerateSearchTextCron = $regenerateSearchTextCron;
    }

    protected function configure(): void
    {
        $this->setDescription('Regenerates search text used by full text search');
        $this->addOption('limit', null, InputOption::VALUE_REQUIRED, 'limit of records to update');
        $this->addOption('limit-time-seconds', null, InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $regenerateSearchTextDto = new RegenerateSearchTextDto();
        if ($input->getOption('limit')) {
            $regenerateSearchTextDto->setLimit((int) $input->getOption('limit'));
        }
        if ($input->getOption('limit-time-seconds')) {
            $regenerateSearchTextDto->setTimeLimitSeconds((int) $input->getOption('limit-time-seconds'));
        }
        $this->regenerateSearchTextCron->regenerate($regenerateSearchTextDto);

        $io->success('done');

        return ConsoleReturnEnum::SUCCESS;
    }
}
