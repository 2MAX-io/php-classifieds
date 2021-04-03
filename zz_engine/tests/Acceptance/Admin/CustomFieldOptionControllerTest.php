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
 */
class CustomFieldOptionControllerTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return [
            'app_admin_custom_field_option_add',
            'app_admin_custom_field_option_edit',
        ];
    }

    public function testAdd(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $client->request('GET', $this->getRouter()->generate('app_admin_custom_field_option_add', [
            'id' => 1,
        ]));
        $client->submitForm('Save', [
            'custom_field_option[name]' => 'test custom field option',
            'custom_field_option[value]' => 'test-custom-field-option',
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());
        $client->followRedirect();
        self::assertSame('app_admin_custom_field_option_edit', $client->getRequest()->attributes->get('_route'));
    }

    public function testEdit(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $client->request('GET', $this->getRouter()->generate('app_admin_custom_field_option_edit', [
            'id' => 1,
        ]));
        $client->submitForm('Save', [
            'custom_field_option[name]' => 'test custom field option',
            'custom_field_option[value]' => 'test-custom-field-option',
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());
        $client->followRedirect();
        self::assertSame('app_admin_custom_field_option_edit', $client->getRequest()->attributes->get('_route'));
    }
}
