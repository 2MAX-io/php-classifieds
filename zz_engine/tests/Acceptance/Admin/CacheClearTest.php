<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Admin;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @internal
 */
class CacheClearTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    /**
     * @return string[]
     */
    public static function getRouteNames(): array
    {
        return [
            'app_admin_cache_clear',
        ];
    }

    public function test(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $listingId = 1;
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_adminCacheClear');
        $url = $this->getRouter()->generate('app_admin_cache_clear', [
            'id' => $listingId,
        ]);
        $client->request('PATCH', $url, [
            '_token' => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();

        self::assertSame(302, $response->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_admin_cache', $client->getRequest()->get('_route'));
    }
}
