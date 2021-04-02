<?php

declare(strict_types=1);

namespace App\Tests\Smoke;

use App\Helper\FilePath;
use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 */
class ResizeImageControllerTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;

    public static function getRouteNames(): array
    {
        return ['app_resize_image'];
    }

    public function testPage(): void
    {
        $client = static::createClient();
        $this->clearDatabase();

        $testImagePath = FilePath::getStaticPath().'/resized/list/system/blank.png';
        if (\file_exists($testImagePath)) {
            \unlink($testImagePath);
        }

        $url = $this->getRouter()->generate('app_resize_image', [
            'type' => 'list',
            'path' => 'system',
            'file' => 'blank.png',
        ]);
        \ob_start();
        $client->request('GET', $url);
        $response = $client->getResponse();
        \ob_end_clean();
        if (\file_exists($testImagePath)) {
            \unlink($testImagePath);
        }

        self::assertEquals(200, $response->getStatusCode(), (string) $response->getContent());
    }
}
