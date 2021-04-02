<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Admin;

use App\Form\Admin\ExecuteAction\ExecuteActionType;
use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 */
class ExecuteActionOnFilteredTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return [
            'app_admin_listing_execute_on_filtered',
        ];
    }

    public function testChangeCat(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        // submit form
        $client->request('GET', $this->getRouter()->generate('app_admin_listing_execute_on_filtered'));
        $client->submitForm('Execute action on filtered listings', [
            'execute_action[action]' => ExecuteActionType::ACTION_SET_CATEGORY,
            'execute_action[category]' => 3,
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());

        // follow redirect after submit
        $client->followRedirect();
        self::assertSame('app_admin_listing_execute_on_filtered', $client->getRequest()->attributes->get('_route'));
    }

    public function testAddCustomField(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        // submit form
        $client->request('GET', $this->getRouter()->generate('app_admin_listing_execute_on_filtered'));
        $client->submitForm('Execute action on filtered listings', [
            'execute_action[action]' => ExecuteActionType::ACTION_SET_CUSTOM_FIELD_OPTION_WHEN_NOT_SET,
            'execute_action[customFieldOption]' => 2,
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());

        // follow redirect after submit
        $client->followRedirect();
        self::assertSame('app_admin_listing_execute_on_filtered', $client->getRequest()->attributes->get('_route'));
    }
}
