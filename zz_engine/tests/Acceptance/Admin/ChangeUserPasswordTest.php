<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Admin;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Enum\TestUserLoginEnum;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 */
class ChangeUserPasswordTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;
    public const NEW_PASSWORD = 'testnewpassword';

    public static function getRouteNames(): array
    {
        return [
            'app_admin_user_edit',
        ];
    }

    public function testChangePassword(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        // submit form
        $client->request('GET', $this->getRouter()->generate('app_admin_user_edit', [
            'id' => 1,
        ]));
        $client->submitForm('Update', [
            'admin_user_edit[plainPassword][first]' => static::NEW_PASSWORD,
            'admin_user_edit[plainPassword][second]' => static::NEW_PASSWORD,
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());

        // follow redirect after submit
        $client->followRedirect();
        self::assertSame('app_admin_user_edit', $client->getRequest()->attributes->get('_route'));

        $client = static::createClient();
        $client->request('GET', $this->getRouter()->generate('app_login'));
        $client->submitForm('Sign in', [
            'email' => TestUserLoginEnum::LOGIN,
            'password' => static::NEW_PASSWORD,
        ]);
        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());
        $client->followRedirect();
        self::assertSame('app_user_listing_new', $client->getRequest()->attributes->get('_route'));
    }
}
