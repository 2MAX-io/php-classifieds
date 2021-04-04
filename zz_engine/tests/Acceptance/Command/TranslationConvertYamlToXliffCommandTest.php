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
class TranslationConvertYamlToXliffCommandTest extends AppIntegrationTestCase
{
    public function test(): void
    {
        static::createClient();
        $kernel = static::$kernel;
        $application = new Application($kernel);

        $command = $application->find('app:dev:translation:yaml-to-xliff');
        $commandTester = new CommandTester($command);
        $outputFilepath = FilePath::getProjectDir().'/zz_engine/translations/messages.en.xliff';
        $commandTester->execute([
            'sourceFile' => FilePath::getProjectDir().'/zz_engine/translations/messages.en.yaml',
            'outputFile' => $outputFilepath,
        ]);

        $output = $commandTester->getDisplay();
        self::assertStringContainsString('[OK] done', $output);
        if (\file_exists($outputFilepath)) {
            \unlink($outputFilepath);
        }
    }
}
