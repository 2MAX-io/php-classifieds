<?php

declare(strict_types=1);

namespace App\Command\Dev;

use App\Helper\Megabyte;
use App\Service\Dev\GenerateTestData\DevGenerateTestListings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DevGenerateTestListingsCommand extends Command
{
    protected static $defaultName = 'app:dev:generate-test-listings';

    /**
     * @var DevGenerateTestListings
     */
    private $devGenerateTestListings;

    public function __construct(DevGenerateTestListings $devGenerateTestListings)
    {
        parent::__construct();

        $this->devGenerateTestListings = $devGenerateTestListings;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('generates and saves random listings, dev only')
            ->addArgument('count', InputArgument::REQUIRED, 'count')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        ini_set('memory_limit', (string) Megabyte::toByes(1024));
        $io = new SymfonyStyle($input, $output);
        $requestedCount = (int) $input->getArgument('count');

        $this->devGenerateTestListings->generate($requestedCount);

        $io->success('done');
    }
}
