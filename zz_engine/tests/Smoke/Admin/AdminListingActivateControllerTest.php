<?php

declare(strict_types=1);

namespace App\Tests\Smoke\Admin;

use App\Controller\Admin\AdminListingActivateController;
use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @internal
 */
class AdminListingActivateControllerTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return [
            'app_admin_listing_activate_action_on_selected',
        ];
    }

    public function testAcceptSelected(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_activateSelectedListings');
        $url = $this->getRouter()->generate('app_admin_listing_activate_action_on_selected');
        $client->request('POST', $url, [
            'action' => AdminListingActivateController::ACTIVATE_ACTION,
            'selected' => '1',
            '_token' => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();

        self::assertSame(302, $response->getStatusCode());
        self::assertSame('/', $client->getResponse()->headers->get('location'));
    }

    public function testRejectSelected(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_activateSelectedListings');
        $url = $this->getRouter()->generate('app_admin_listing_activate_action_on_selected');
        $client->request('POST', $url, [
            'action' => AdminListingActivateController::REJECT_ACTION,
            'selected' => '1',
            '_token' => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();

        self::assertSame(302, $response->getStatusCode());
        self::assertSame('/', $client->getResponse()->headers->get('location'));
    }
}
