<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Command;

use App\Tests\Base\AppIntegrationTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 */
class DeleteExpiredListingFilesCommandTest extends AppIntegrationTestCase
{
    public function test(): void
    {
        static::createClient();
        $kernel = static::$kernel;
        $application = new Application($kernel);

        $command = $application->find('app:delete-expired-listing-files');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'days' => 1,
        ]);

        $output = $commandTester->getDisplay();
        self::assertStringContainsString('[OK] done', $output);
    }
}
