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
class TranslationFindMissingCommandTest extends AppIntegrationTestCase
{
    public function test(): void
    {
        static::createClient();
        $kernel = static::$kernel;
        $application = new Application($kernel);

        $command = $application->find('app:dev:translation:find-missing');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'sourceFile' => FilePath::getProjectDir().'/zz_engine/translations/messages.en.yaml',
        ]);

        $output = $commandTester->getDisplay();
        self::assertStringContainsString('no missing translations', $output);
    }
}
