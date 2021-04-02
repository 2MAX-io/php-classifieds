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
class AdminCategoryControllerTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return [
            'app_admin_category_new',
            'app_admin_category_edit',
        ];
    }

    public function testAddCategory(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $client->request('GET', $this->getRouter()->generate('app_admin_category_new'));
        $client->submitForm('Save', [
            'admin_category_save[name]' => 'test cat edit',
            'admin_category_save[slug]' => 'test-cat-slug',
            'admin_category_save[parent]' => '2',
            'admin_category_save[sort]' => '101',
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());
        $client->followRedirect();
        self::assertSame('app_admin_category_edit', $client->getRequest()->attributes->get('_route'));
    }

    public function testEditCategory(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $client->request('GET', $this->getRouter()->generate('app_admin_category_edit', [
            'id' => 2,
        ]));
        $client->submitForm('Update', [
            'admin_category_save[name]' => 'test cat edit',
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());
        $client->followRedirect();
        self::assertSame('app_admin_category_edit', $client->getRequest()->attributes->get('_route'));
    }
}
