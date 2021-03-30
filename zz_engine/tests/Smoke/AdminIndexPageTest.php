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
class AdminIndexPageTest extends WebTestCase
{
    use LoginTrait;

    public function testIndexPage(): void
    {
        $client = static::createClient();
        $this->loginAdmin($client);
        $url = $this->getRouter()->generate('app_admin_index');

        $client->request('GET', $url);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }

    protected function getRouter(): RouterInterface
    {
        return static::$kernel->getContainer()->get('router');
    }
}
