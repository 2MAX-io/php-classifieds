<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\UserListing;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 */
class ValidityExtendTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return ['app_user_validity_extend'];
    }

    public function testUserRegister(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);

        $client->request('GET', $this->getRouter()->generate('app_user_validity_extend', [
            'id' => 1,
        ]));
        $client->submitForm('Extend validity', [
            'validity_extend[validityTimeDays]' => 31,
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_user_validity_extend', $client->getRequest()->attributes->get('_route'));
    }
}
