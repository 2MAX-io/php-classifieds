<?php

declare(strict_types=1);

namespace App\Tests\Smoke;

use App\Tests\Base\AppIntegrationTest;
use App\Tests\Base\DatabaseTestHelper;
use App\Tests\Base\LoginTrait;
use Symfony\Component\Routing\RouterInterface;

/**
 * @internal
 * @coversNothing
 */
class UserMyAccountPageTest extends AppIntegrationTest
{
    use LoginTrait;
    use DatabaseTestHelper;

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

    protected function getRouter(): RouterInterface
    {
        return static::$kernel->getContainer()->get('router');
    }
}
