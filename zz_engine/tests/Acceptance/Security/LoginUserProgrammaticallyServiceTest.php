<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Security;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 */
class LoginUserProgrammaticallyServiceTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return [
            'app_test_login_programmatically_user',
            'app_test_login_programmatically_admin',
        ];
    }

    public function testProgrammaticallyLoginUser(): void
    {
        $client = static::createClient();
        $this->clearDatabase();

        $client->request('GET', $this->getRouter()->generate('app_test_login_programmatically_user', [
            'urlSecret' => $_ENV['APP_NOT_PUBLIC_URL_SECRET'],
        ]));

        $client->request('GET', $this->getRouter()->generate('app_user_my_account'));
        self::assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testProgrammaticallyLoginAdmin(): void
    {
        $client = static::createClient();
        $this->clearDatabase();

        $client->request('GET', $this->getRouter()->generate('app_test_login_programmatically_admin', [
            'urlSecret' => $_ENV['APP_NOT_PUBLIC_URL_SECRET'],
        ]));

        $client->request('GET', $this->getRouter()->generate('app_admin_index'));
        self::assertSame(200, $client->getResponse()->getStatusCode());
    }
}
