<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\UserListing;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Enum\TestDataEnum;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 */
class ExtendExpirationTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return ['app_user_extend_expiration'];
    }

    public function testExtendExpiration(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);

        $client->request('GET', $this->getRouter()->generate('app_user_extend_expiration', [
            'id' => 1,
        ]));
        $client->submitForm('Extend validity', [
            'extend_expiration[package]' => TestDataEnum::FREE_PACKAGE_ID,
        ]);
        $response = $client->getResponse();
        self::assertSame(302, $response->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_user_extend_expiration', $client->getRequest()->attributes->get('_route'));
    }
}
