<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

trait MessengerTestTrait
{
    private function processMessenger(string $queueName): void
    {
        while ($this->hasNextMessage($queueName)) {
            $this->getTestContainer()->get(EntityManagerInterface::class)->clear();
            $application = new Application(static::$kernel);
            $command = $application->find('messenger:consume');
            $commandTester = new CommandTester($command);
            $commandTester->execute([
                'receivers' => [$queueName],
                '--limit' => 1,
                '--time-limit' => 5,
            ]);
        }
    }

    private function hasNextMessage(string $queueName): bool
    {
        $pdo = $this->getTestContainer()->get(EntityManagerInterface::class)->getConnection();
        $stmt = $pdo->executeQuery(<<<'EOT'
SELECT id FROM zzzz_messenger_messages WHERE queue_name=:queueName LIMIT 1
EOT, [
            'queueName' => $queueName,
        ]);

        return false !== $stmt->fetchOne();
    }

    private function assertNoFailedMessages(): void
    {
        $pdo = $this->getTestContainer()->get(EntityManagerInterface::class)->getConnection();
        $stmt = $pdo->executeQuery(<<<'EOT'
SELECT COUNT(1) FROM zzzz_messenger_messages WHERE queue_name='failed' || delivered_at IS NOT NULL
EOT);

        self::assertSame(0, (int) $stmt->fetchOne());
    }

    private function assertMessageQueueEmpty(): void
    {
        $pdo = $this->getTestContainer()->get(EntityManagerInterface::class)->getConnection();
        $stmt = $pdo->executeQuery(<<<'EOT'
SELECT COUNT(1) FROM zzzz_messenger_messages WHERE 1
EOT);

        self::assertSame(0, (int) $stmt->fetchOne());
    }
}
