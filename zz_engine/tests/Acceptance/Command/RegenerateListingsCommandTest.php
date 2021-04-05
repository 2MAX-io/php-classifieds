<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Command;

use App\Tests\Base\AppIntegrationTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 */
class RegenerateListingsCommandTest extends AppIntegrationTestCase
{
    public function test(): void
    {
        static::createClient();
        $kernel = static::$kernel;
        $application = new Application($kernel);

        $command = $application->find('app:regenerate-listings');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--limit' => 2,
            '--limit-time-seconds' => 10,
        ]);

        $output = $commandTester->getDisplay();
        self::assertStringContainsString('[OK] done', $output);
    }
}
