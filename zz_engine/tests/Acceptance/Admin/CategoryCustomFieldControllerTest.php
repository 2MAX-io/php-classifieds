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
class CategoryCustomFieldControllerTest extends AppIntegrationTestCase
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public function test(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $client->request('GET', $this->getRouter()->generate('app_admin_category_add_custom_field', [
            'id' => 2,
        ]));
        $client->submitForm('Save', [
            'category_add_custom_field[customField]' => 11,
        ]);
        $response = $client->getResponse();
        self::assertSame(302, $response->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_admin_category_edit', $client->getRequest()->attributes->get('_route'));
    }
}
