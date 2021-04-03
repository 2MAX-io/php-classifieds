<?php

declare(strict_types=1);

namespace App\Tests\Smoke;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 */
class CronControllerTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;

    public static function getRouteNames(): array
    {
        return [
            'app_cron',
        ];
    }

    public function testCronViaUrl(): void
    {
        $client = static::createClient();
        $this->clearDatabase();

        $url = $this->getRouter()->generate('app_cron', [
            'urlSecret' => $_ENV['APP_NOT_PUBLIC_URL_SECRET'],
        ]);
        \ob_start();
        $client->request('GET', $url);
        $response = $client->getResponse();
        $responseContent = (string) $response->getContent();
        \ob_end_clean();
        self::assertEquals(200, $response->getStatusCode(), $responseContent);
    }
}
