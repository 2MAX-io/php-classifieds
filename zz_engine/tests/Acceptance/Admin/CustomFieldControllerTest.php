<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Admin;

use App\Entity\CustomField;
use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 */
class CustomFieldControllerTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return [
            'app_admin_custom_field_new',
            'app_admin_custom_field_edit',
        ];
    }

    public function testAdd(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $client->request('GET', $this->getRouter()->generate('app_admin_custom_field_new'));
        $client->submitForm('Save', [
            'custom_field[name]' => 'test custom field name',
            'custom_field[type]' => CustomField::SELECT_SINGLE,
        ]);
        $response = $client->getResponse();
        self::assertSame(302, $response->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_admin_custom_field_edit', $client->getRequest()->attributes->get('_route'));
    }

    public function testEdit(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $client->request('GET', $this->getRouter()->generate('app_admin_custom_field_edit', [
            'id' => 1,
        ]));
        $client->submitForm('Update', [
            'custom_field[name]' => 'test custom field name',
        ]);
        $response = $client->getResponse();
        self::assertSame(302, $response->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_admin_custom_field_edit', $client->getRequest()->attributes->get('_route'));
    }
}
