<?php

declare(strict_types=1);

namespace App\Tests\Smoke;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 * @coversNothing
 */
class UserInvoiceDownloadControllerTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return ['app_user_invoice_download'];
    }

    public function test(): void
    {
        self::markTestSkipped('todo problem with testing stream response'); // todo
        /* @phpstan-ignore-next-line */
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);

        $url = $this->getRouter()->generate('app_user_invoice_download', [
            'invoice' => 1,
        ]);
        \ob_start();
        $client->request('GET', $url);
        $response = $client->getResponse();
        \ob_end_clean();

        self::assertEquals(200, $response->getStatusCode(), (string) $response->getContent());
    }
}
