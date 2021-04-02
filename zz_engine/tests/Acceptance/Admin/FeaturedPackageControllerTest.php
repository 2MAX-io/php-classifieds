<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Admin;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 * @coversNothing
 */
class FeaturedPackageControllerTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return [
            'app_admin_featured_package_new',
            'app_admin_featured_package_edit',
        ];
    }

    public function testAdd(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $client->request('GET', $this->getRouter()->generate('app_admin_featured_package_new'));
        $client->submitForm('Save', [
            'featured_package[name]' => 'test feature package name',
            'featured_package[adminName]' => 'test feature package name for admin',
            'featured_package[priceFloat]' => '1',
            'featured_package[daysFeaturedExpire]' => '1',
            'featured_package[daysListingExpire]' => '1',
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());
        $client->followRedirect();
        self::assertSame('app_admin_featured_package_edit', $client->getRequest()->attributes->get('_route'));
    }

    public function testEdit(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $client->request('GET', $this->getRouter()->generate('app_admin_featured_package_edit', [
            'id' => 1,
        ]));
        $client->submitForm('Update', [
            'featured_package[name]' => 'test feature package name',
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());
        $client->followRedirect();
        self::assertSame('app_admin_featured_package_edit', $client->getRequest()->attributes->get('_route'));
    }
}
