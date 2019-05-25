<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Cron\RegenerateSearchTextCron;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SearchTextRegenerateCommand extends Command
{
    protected static $defaultName = 'app:search-text-regenerate';

    /**
     * @var RegenerateSearchTextCron
     */
    private $regenerateSearchTextCron;

    public function __construct(RegenerateSearchTextCron $regenerateSearchTextCron)
    {
        parent::__construct();

        $this->regenerateSearchTextCron = $regenerateSearchTextCron;
    }

    protected function configure()
    {
        $this->setDescription('Regenerates search text used by full text search');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $this->regenerateSearchTextCron->regenerate();

        $io->success('done');
    }
}
