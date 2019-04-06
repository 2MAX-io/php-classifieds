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

        // check listing displayed correctly
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->request('GET', $this->getRouter()->generate('app_listing_show', [
            'id' => 1,
            'slug' => 'test-listing-title',
        ]));
        self::assertStringContainsString('Listing has been rejected', (string) $client->getResponse()->getContent());
        self::assertStringNotContainsString('Show contact information', (string) $client->getResponse()->getContent());
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
