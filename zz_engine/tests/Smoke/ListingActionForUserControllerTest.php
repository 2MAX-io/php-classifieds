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
class ListingActionForUserControllerTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return [
            'app_user_listing_deactivate',
            'app_user_listing_activate',
            'app_user_listing_remove',
        ];
    }

    public function testDeactivate(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);

        $listingId = 1;
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_deactivate'.$listingId);
        $url = $this->getRouter()->generate('app_user_listing_deactivate', [
            'id' => $listingId,
        ]);
        $client->request('PATCH', $url, [
            '_token' => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();

        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());
        self::assertEquals('/', $client->getResponse()->headers->get('location'));
    }

    public function testActivate(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);

        $listingId = 1;
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_activate'.$listingId);
        $url = $this->getRouter()->generate('app_user_listing_activate', [
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
        $this->loginUser($client);

        $listingId = 1;
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_remove'.$listingId);
        $url = $this->getRouter()->generate('app_user_listing_remove', [
            'id' => $listingId,
        ]);
        $client->request('DELETE', $url, [
            '_token' => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();

        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());
        self::assertEquals('/', $client->getResponse()->headers->get('location'));
    }
}
