<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\User;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 */
class UserSettingsControllerTest extends AppIntegrationTestCase
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public function test(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);

        // submit form
        $client->request('GET', $this->getRouter()->generate('app_user_settings'));
        $client->submitForm('Save');
        $response = $client->getResponse();
        self::assertSame(302, $response->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_user_settings', $client->getRequest()->attributes->get('_route'));
    }
}
