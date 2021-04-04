<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Admin;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 */
class ListingRejectControllerTest extends AppIntegrationTestCase
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public function test(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $client->request('GET', $this->getRouter()->generate('app_admin_listing_reject', [
            'id' => 1,
        ]));
        $client->submitForm('Reject');
        self::assertSame(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_admin_listing_reject', $client->getRequest()->get('_route'));
    }

    public function testWithReason(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $client->request('GET', $this->getRouter()->generate('app_admin_listing_reject', [
            'id' => 1,
        ]));
        $client->submitForm('Reject', [
            'admin_reject_listing[rejectionReason]' => 'test rejection reason',
        ]);
        self::assertSame(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_admin_listing_reject', $client->getRequest()->get('_route'));
    }
}
