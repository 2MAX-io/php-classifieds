<?php

declare(strict_types=1);

namespace App\Tests\Migrations;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Traits\DatabaseTestTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 * @coversNothing
 */
class MigrationsTest extends AppIntegrationTestCase
{
    use DatabaseTestTrait;

    public function testSchemaUpdateForceEmptyFromInstall(): void
    {
        $client = static::createClient();
        $this->clearDatabase();

        $kernel = $client->getKernel();
        $application = new Application($kernel);

        $command = $application->find('doctrine:schema:update');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--force' => true,
            '--no-interaction' => true,
            '--env' => 'test',
        ], [
            'interactive' => false,
        ]);
        $output = $commandTester->getDisplay();
        self::assertStringContainsString('Nothing to update - your database is already in sync with the current', $output);
    }

    public function testNoMigrationsFromInstallToExecute(): void
    {
        $client = static::createClient();
        $this->clearDatabase();

        $kernel = $client->getKernel();
        $application = new Application($kernel);
        $command = $application->find('doctrine:migrations:migrate');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--no-interaction',
            '--env' => 'test',
        ], [
            'interactive' => false,
        ]);

        $output = $commandTester->getDisplay();
        self::assertStringContainsString('[OK] Already at the latest version', $output);
        $this->assertNoDiffWithDoctrineMapping();
    }

    public function testMigrationsFromEmptyDatabase(): void
    {
        $client = static::createClient();
        $this->dropAndCreateDatabase();

        $kernel = $client->getKernel();
        $application = new Application($kernel);
        $command = $application->find('doctrine:migrations:migrate');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--no-interaction',
            '--env' => 'test',
        ], [
            'interactive' => false,
        ]);

        $output = $commandTester->getDisplay();
        self::assertStringContainsString('[notice] Migrating up to', $output);
        $this->assertNoDiffWithDoctrineMapping();
    }

    public function testSchemaUpdateForceEmptyFromEmptyDatabase(): void
    {
        $client = static::createClient();
        $this->dropAndCreateDatabase();

        $kernel = $client->getKernel();
        $application = new Application($kernel);

        $command = $application->find('doctrine:migrations:migrate');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--no-interaction',
            '--env' => 'test',
        ], [
            'interactive' => false,
        ]);

        $command = $application->find('doctrine:schema:update');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--force' => true,
            '--no-interaction' => true,
            '--env' => 'test',
        ], [
            'interactive' => false,
        ]);
        $output = $commandTester->getDisplay();
        self::assertStringContainsString('Nothing to update - your database is already in sync with the current', $output);
    }

    private function assertNoDiffWithDoctrineMapping(): void
    {
        $em = $this->getTestContainer()->get(EntityManagerInterface::class);
        $metadata = $em->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($em);

        $sql = $schemaTool->getUpdateSchemaSql($metadata);

        self::assertSame([
            'DROP TABLE zzzz_doctrine_migration_versions',
        ], $sql);
    }
}
