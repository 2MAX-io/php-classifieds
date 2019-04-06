<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Command;

use App\Helper\FilePath;
use App\Tests\Base\AppIntegrationTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 */
class TranslationDiffCommandTest extends AppIntegrationTestCase
{
    public function test(): void
    {
        static::createClient();
        $kernel = static::$kernel;
        $application = new Application($kernel);

        $command = $application->find('app:dev:translation:diff');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'sourceFileA' => FilePath::getProjectDir().'/zz_engine/translations/messages.en.yaml',
            'sourceFileB' => FilePath::getProjectDir().'/zz_engine/translations/messages.pl.yaml',
        ]);

        $output = $commandTester->getDisplay();
        self::assertStringContainsString('no differences', $output);
    }
}
