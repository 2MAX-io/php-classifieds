<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Account;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Enum\TestLoginEnum;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 * @coversNothing
 */
class LogoutTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return ['app_logout'];
    }

    public function testLogout(): void
    {
        $client = static::createClient();
        $this->clearDatabase();

        // login
        $client->request('GET', $this->getRouter()->generate('app_login'));
        $client->submitForm('Sign in', [
            'email' => TestLoginEnum::LOGIN,
            'password' => TestLoginEnum::PASSWORD,
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());
        $client->followRedirect();
        self::assertSame('app_user_listing_new', $client->getRequest()->attributes->get('_route'));

        // assert can access user page
        $client->request('GET', $this->getRouter()->generate('app_user_my_account'));
        self::assertEquals(200, $client->getResponse()->getStatusCode(), (string) $client->getResponse()->getContent());

        // logout
        $client->request('GET', $this->getRouter()->generate('app_logout'));
        self::assertEquals(302, $client->getResponse()->getStatusCode(), (string) $client->getResponse()->getContent());
        self::assertEquals('http://localhost/', $client->getResponse()->headers->get('location'));

        // assert can not access after logout
        $client->request('GET', $this->getRouter()->generate('app_user_my_account'));
        self::assertEquals(302, $client->getResponse()->getStatusCode(), (string) $client->getResponse()->getContent());
        $client->followRedirect();
        self::assertSame('app_login', $client->getRequest()->attributes->get('_route'));
    }
}
