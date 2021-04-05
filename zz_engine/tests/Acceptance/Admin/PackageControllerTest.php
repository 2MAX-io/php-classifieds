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
class PackageControllerTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return [
            'app_admin_package_new',
            'app_admin_package_edit',
        ];
    }

    public function testAdd(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $client->request('GET', $this->getRouter()->generate('app_admin_package_new'));
        $client->submitForm('Save', [
            'package[name]' => 'test feature package name',
            'package[adminName]' => 'test feature package name for admin',
            'package[priceFloat]' => '1',
            'package[daysFeaturedExpire]' => '1',
            'package[daysListingExpire]' => '1',
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_admin_package_edit', $client->getRequest()->attributes->get('_route'));
    }

    public function testEdit(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $crawler = $client->request('GET', $this->getRouter()->generate('app_admin_package_edit', [
            'id' => 1,
        ]));
        $submitButton = $crawler->selectButton('Update');
        $form = $submitButton->form([
            'package[name]' => 'test feature package name',
        ]);
        $values = $form->getPhpValues();
        $values['selectedCategories'][] = 3;
        $client->request(
            $form->getMethod(),
            $form->getUri(),
            $values,
            $form->getPhpFiles()
        );
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_admin_package_edit', $client->getRequest()->attributes->get('_route'));
    }
}
