<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Command;

use App\Tests\Base\AppIntegrationTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 */
class DevGenerateTestListingsCommandTest extends AppIntegrationTestCase
{
    public function test(): void
    {
        static::createClient();
        $kernel = static::$kernel;
        $application = new Application($kernel);

        $command = $application->find('app:dev:generate-test-listings');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'count' => 1,
        ]);

        $output = $commandTester->getDisplay();
        self::assertStringContainsString('[OK] done', $output);
    }
}
