<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Cron;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\MessengerTestTrait;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 */
class CronTest extends AppIntegrationTestCase
{
    use DatabaseTestTrait;
    use MessengerTestTrait;

    public function testCron(): void
    {
        static::createClient();
        $this->clearDatabase();
        $kernel = static::$kernel;
        $application = new Application($kernel);

        $command = $application->find('app:cron:main');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--night' => '--night',
            '--ignore-delay' => '--ignore-delay',
        ]);

        $this->processMessenger('async');
        $this->processMessenger('one_at_time');

        $this->assertNoFailedMessages();
        $this->assertMessageQueueEmpty();
    }
}
