<?php

declare(strict_types=1);

namespace App\Command\Cron;

use App\Service\Cron\CronService;
use Doctrine\ORM\EntityManagerInterface;
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

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(CronService $cronService, EntityManagerInterface $em)
    {
        parent::__construct();

        $this->cronService = $cronService;
        $this->em = $em;
    }

    protected function configure()
    {
        $this->setDescription('Main cron');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $this->cronService->run();

        $this->em->flush();

        $io->success('done');
    }
}
