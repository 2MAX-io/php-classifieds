<?php

declare(strict_types=1);

namespace App\Tests\Smoke;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 * @coversNothing
 */
class UserMyAccountPageTest extends AppIntegrationTestCase
{
    use LoginTestTrait;
    use DatabaseTestTrait;
    use RouterTestTrait;

    public function testUserMyAccountPage(): void
    {
        $client = static::createClient();
        $GLOBALS['kernel'] = static::$kernel;
        $this->clearDatabase();
        $this->loginUser($client);
        $url = $this->getRouter()->generate('app_user_my_account');

        $client->request('GET', $url);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
