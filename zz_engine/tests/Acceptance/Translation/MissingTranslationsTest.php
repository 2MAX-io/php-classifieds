<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Translation;

use App\Helper\FilePath;
use App\Service\System\Dev\MissingTranslations\FindMissingTranslationsService;
use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Traits\DatabaseTestTrait;

/**
 * @internal
 */
class MissingTranslationsTest extends AppIntegrationTestCase
{
    use DatabaseTestTrait;

    public function test(): void
    {
        static::createClient();

        $findMissingTranslationsService = self::$container->get(FindMissingTranslationsService::class);
        $missingTranslations = $findMissingTranslationsService->findMissingTranslations(
            FilePath::getProjectDir().'/zz_engine/translations/messages.en.yaml'
        );

        self::assertCount(0, $missingTranslations, \implode("\n", $missingTranslations));
    }
}
