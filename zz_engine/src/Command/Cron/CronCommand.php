<?php

declare(strict_types=1);

namespace App\Command\Cron;

use App\Enum\ConsoleReturnEnum;
use App\Service\Cron\CronService;
use App\Service\Cron\Dto\CronMainDto;
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
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $cronMainDto = new CronMainDto();
        if ($input->getOption('ignore-delay')) {
            $cronMainDto->setIgnoreDelay(true);
        }
        $this->cronService->run($cronMainDto);

        $this->em->flush();

        $io->success('done');

        return ConsoleReturnEnum::SUCCESS;
    }
}
