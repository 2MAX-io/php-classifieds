<?php

namespace App\Command\Cron;

use App\Service\Cron\CronService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CronCommand extends Command
{
    protected static $defaultName = 'app:cron:main';

    /**
     * @var CronService
     */
    private $cronService;

    public function __construct(CronService $cronService)
    {
        parent::__construct();

        $this->cronService = $cronService;
    }

    protected function configure()
    {
        $this->setDescription('Main cron');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $this->cronService->run();

        $io->success('done');
    }
}
