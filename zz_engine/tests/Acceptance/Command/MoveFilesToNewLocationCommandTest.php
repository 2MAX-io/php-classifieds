<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Command;

use App\Helper\FilePath;
use App\Tests\Base\AppIntegrationTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Webmozart\PathUtil\Path;

/**
 * @internal
 */
class MoveFilesToNewLocationCommandTest extends AppIntegrationTestCase
{
    public function testDryRunEmpty(): void
    {
        static::createClient();
        $kernel = static::$kernel;
        $application = new Application($kernel);

        $command = $application->find('app:move-files-new-location');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--dry-run' => '--dry-run',
            '--limit' => 1,
        ]);

        $output = $commandTester->getDisplay();
        self::assertStringContainsString('[OK] done', $output);
    }

    public function testDryRun(): void
    {
        static::createClient();
        $kernel = static::$kernel;
        $application = new Application($kernel);

        $legacyFilePath = FilePath::getPublicDir().'/static/listing/0000_legacy/test_file_on_legacyPath.png';
        $expectedNewFilePath = FilePath::getPublicDir().'/static/listing/1/user_1/listing_1/test_file_on_legacyPath.png';
        if (\file_exists($expectedNewFilePath)) {
            \unlink($expectedNewFilePath);
        }
        \copy(FilePath::getProjectDir().'/static/system/1920x1080.png', $legacyFilePath);
        $pdo = $this->getTestContainer()->get(EntityManagerInterface::class)->getConnection();
        $pdo->executeQuery('UPDATE listing_file SET path = :path WHERE id = 1', [
            ':path' => Path::makeRelative($legacyFilePath, FilePath::getPublicDir()),
        ]);

        self::assertFileDoesNotExist($expectedNewFilePath);

        $command = $application->find('app:move-files-new-location');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--dry-run' => '--dry-run',
            '--limit' => 1,
        ]);

        $output = $commandTester->getDisplay();
        self::assertStringContainsString('[OK] done', $output);
        self::assertFileExists($legacyFilePath);
        self::assertFileDoesNotExist($expectedNewFilePath);
        \unlink($legacyFilePath);
        if (\file_exists($expectedNewFilePath)) {
            \unlink($expectedNewFilePath);
        }
    }

    public function testRealMove(): void
    {
        static::createClient();
        $kernel = static::$kernel;
        $application = new Application($kernel);

        $legacyFilePath = FilePath::getPublicDir().'/static/listing/0000_legacy/test_file_on_legacyPath.png';
        $expectedNewFilePath = FilePath::getPublicDir().'/static/listing/1/user_1/listing_1/test_file_on_legacyPath.png';
        if (\file_exists($expectedNewFilePath)) {
            \unlink($expectedNewFilePath);
        }
        \copy(FilePath::getProjectDir().'/static/system/1920x1080.png', $legacyFilePath);
        $pdo = $this->getTestContainer()->get(EntityManagerInterface::class)->getConnection();
        $pdo->executeQuery('UPDATE listing_file SET path = :path WHERE id = 1', [
            ':path' => Path::makeRelative($legacyFilePath, FilePath::getPublicDir()),
        ]);

        self::assertFileDoesNotExist($expectedNewFilePath);

        $command = $application->find('app:move-files-new-location');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        self::assertStringContainsString('[OK] done', $output);
        self::assertFileExists($expectedNewFilePath);
        \unlink($expectedNewFilePath);
    }
}
