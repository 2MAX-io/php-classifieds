<?php

declare(strict_types=1);

namespace App\Command\Cron;

use App\Enum\ConsoleReturnEnum;
use App\Service\System\Cron\CronService;
use App\Service\System\Cron\Dto\CronDto;
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

    protected function configure(): void
    {
        $this->setDescription('Main cron');
        $this->addOption('ignore-delay');
        $this->addOption('night');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $cronDto = new CronDto();
        if ($input->getOption('ignore-delay')) {
            $cronDto->setIgnoreDelay(true);
        }
        if ($input->getOption('night')) {
            $cronDto->setNight(true);
        }
        $this->cronService->run($cronDto);

        $this->em->flush();

        $io->success('done');

        return ConsoleReturnEnum::SUCCESS;
    }
}
