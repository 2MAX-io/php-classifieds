<?php

declare(strict_types=1);

namespace App\Tests\Smoke;

use App\Tests\Base\LoginTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

/**
 * @internal
 * @coversNothing
 */
class UserMyAccountPageTest extends WebTestCase
{
    use LoginTrait;

    public function testUserMyAccountPage(): void
    {
        $client = static::createClient();
        $GLOBALS['kernel'] = static::$kernel;
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
