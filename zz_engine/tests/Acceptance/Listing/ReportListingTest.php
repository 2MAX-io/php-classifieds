<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Listing;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Enum\TestUserLoginEnum;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 */
class ReportListingTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return [
            'app_report_listing',
        ];
    }

    public function testReportListingAnonymous(): void
    {
        $client = static::createClient();
        $this->clearDatabase();

        // submit form
        $client->request('GET', $this->getRouter()->generate('app_report_listing', [
            'listing' => 1,
        ]));
        $client->submitForm('Report abuse', [
            'listing_report[reportMessage]' => 'test report message',
            'listing_report[email]' => 'test-report-listing@example.com',
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode());

        // follow redirect after submit
        $client->followRedirect();
        self::assertSame('app_report_listing', $client->getRequest()->attributes->get('_route'));
    }

    public function testReportListingUser(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);

        // submit form
        $client->request('GET', $this->getRouter()->generate('app_report_listing', [
            'listing' => 1,
        ]));
        $client->submitForm('Report abuse', [
            'listing_report[reportMessage]' => 'test report message',
            'listing_report[email]' => TestUserLoginEnum::LOGIN,
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode());

        // follow redirect after submit
        $client->followRedirect();
        self::assertSame('app_report_listing', $client->getRequest()->attributes->get('_route'));
    }
}
