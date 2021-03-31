<?php

declare(strict_types=1);

namespace App\Tests\Smoke;

use App\Helper\FilePath;
use App\Tests\Base\AppIntegrationTest;
use App\Tests\Base\DatabaseTestHelper;
use App\Tests\Base\RouterHelper;

/**
 * @internal
 * @coversNothing
 */
class ResizeImageControllerTest extends AppIntegrationTest
{
    use DatabaseTestHelper;
    use RouterHelper;

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
