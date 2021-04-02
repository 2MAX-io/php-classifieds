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
class AdminListingEditAdvancedControllerTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return ['app_admin_listing_edit_advanced'];
    }

    public function testEditListingAdvanced(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $client->request('GET', $this->getRouter()->generate('app_admin_listing_edit_advanced', [
            'id' => 1,
        ]));
        $client->submitForm('Save', [
            'admin_listing_advanced_edit[rejectionReason]' => 'test rejection reason',
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());
        $client->followRedirect();
        self::assertSame('app_admin_listing_edit_advanced', $client->getRequest()->attributes->get('_route'));
    }
}
