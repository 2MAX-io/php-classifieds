<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Admin;

use App\Helper\FilePath;
use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 */
class UpgradeControllerTest extends AppIntegrationTestCase
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public function test(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $testUpgradedFilePath = FilePath::getProjectDir().'/zz_engine/var/cache/upgrade/test-upgrade-executed.php';
        if (\file_exists($testUpgradedFilePath)) {
            \unlink($testUpgradedFilePath);
        }
        self::assertFileDoesNotExist($testUpgradedFilePath);

        $client->request('GET', $this->getRouter()->generate('app_admin_upgrade'));
        self::assertStringContainsString('New version available, you can upgrade now', (string) $client->getResponse()->getContent());
        $client->submitForm('Execute upgrade');
        $response = $client->getResponse();
        self::assertSame(200, $response->getStatusCode());
        self::assertStringContainsString('Upgrade has been executed', (string) $client->getResponse()->getContent());
        self::assertFileExists($testUpgradedFilePath);
        \unlink($testUpgradedFilePath);
    }
}
