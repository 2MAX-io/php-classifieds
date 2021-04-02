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
class LicenseSettingTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return [
            'app_admin_settings_license',
        ];
    }

    public function testLicenseSettingsValid(): void
    {
        if (!isset($_ENV['APP_TEST_LICENSE'])) {
            self::markTestSkipped('could not find license in ENV: `APP_TEST_LICENSE`');
        }

        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $client->request('GET', $this->getRouter()->generate('app_admin_settings_license'));
        $client->submitForm('Save', [
            'license_settings[license]' => $_ENV['APP_TEST_LICENSE'],
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());
        $client->followRedirect();
        self::assertSame('app_admin_settings_license', $client->getRequest()->attributes->get('_route'));
    }

    public function testLicenseSettingsInvalid(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $client->request('GET', $this->getRouter()->generate('app_admin_settings_license'));
        $client->submitForm('Save', [
            'license_settings[license]' => 'invalid license',
        ]);
        $response = $client->getResponse();
        self::assertEquals(200, $response->getStatusCode(), (string) $response->getContent());
        self::assertStringContainsString('License could not be decoded', (string) $response->getContent());
    }
}
