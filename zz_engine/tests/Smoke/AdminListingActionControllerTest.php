<?php

declare(strict_types=1);

namespace App\Tests\Smoke;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @internal
 * @coversNothing
 */
class AdminListingActionControllerTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return [
            'app_admin_listing_activate',
            'app_admin_listing_remove',
            'app_admin_listing_pull_up',
            'app_admin_listing_feature_for_week',
            'app_admin_listing_redirect_next_waiting_activation',
        ];
    }

    public function testActivate(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $listingId = 1;
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_adminActivateListing'.$listingId);
        $url = $this->getRouter()->generate('app_admin_listing_activate', [
            'id' => $listingId,
        ]);
        $client->request('PATCH', $url, [
            '_token' => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();

        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());
        self::assertEquals('/', $client->getResponse()->headers->get('location'));
    }

    public function testRemove(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $listingId = 1;
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_adminRemoveListing'.$listingId);
        $url = $this->getRouter()->generate('app_admin_listing_remove', [
            'id' => $listingId,
        ]);
        $client->request('DELETE', $url, [
            '_token' => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();

        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());
        self::assertEquals('/', $client->getResponse()->headers->get('location'));
    }

    public function testPullUp(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $listingId = 1;
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_adminPullUpListing'.$listingId);
        $url = $this->getRouter()->generate('app_admin_listing_pull_up', [
            'id' => $listingId,
        ]);
        $client->request('PATCH', $url, [
            '_token' => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();

        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());
    }

    public function testFeatureForWeek(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $listingId = 1;
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_adminFeatureForWeekListing'.$listingId);
        $url = $this->getRouter()->generate('app_admin_listing_feature_for_week', [
            'id' => $listingId,
        ]);
        $client->request('PATCH', $url, [
            '_token' => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();

        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());
        self::assertEquals('/', $client->getResponse()->headers->get('location'));
    }

    public function testRedirectToNextListingWaitingActivation(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_adminRedirectNextWaitingActivation');
        $url = $this->getRouter()->generate('app_admin_listing_redirect_next_waiting_activation');
        $client->request('PATCH', $url, [
            '_token' => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();

        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());
        self::assertEquals('/l/1/test-listing-title?showListingPreviewForOwner=1', $client->getResponse()->headers->get('location'));
    }
}
