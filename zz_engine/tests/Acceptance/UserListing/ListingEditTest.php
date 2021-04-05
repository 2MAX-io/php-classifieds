<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\UserListing;

use App\Helper\FilePath;
use App\Helper\JsonHelper;
use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;
use Webmozart\PathUtil\Path;

/**
 * @internal
 */
class ListingEditTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return ['app_user_listing_edit'];
    }

    public function testEditListing(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);

        $client->request('GET', $this->getRouter()->generate('app_user_listing_edit', [
            'id' => 1,
        ]));
        $client->submitForm('Update', [
            'listing[title]' => 'test listing',
            'listing[description]' => 'test listing',
        ]);
        $response = $client->getResponse();
        self::assertSame(302, $response->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_user_listing_edit', $client->getRequest()->attributes->get('_route'));
    }

    public function testUploadUnsafeFile(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);

        $testUploadedFilePath = FilePath::getTempFileUpload().'/test_unsafe_file_upload.php';
        \copy(FilePath::getPublicDir().'/static/system/1920x1080.png', $testUploadedFilePath);

        $crawler = $client->request('GET', $this->getRouter()->generate('app_user_listing_edit', [
            'id' => 1,
        ]));
        $submitButton = $crawler->selectButton('Update');
        $form = $submitButton->form([
            'listing[title]' => 'test listing',
            'listing[description]' => 'test listing',
        ]);
        $values = $form->getPhpValues();
        $values['fileuploader-list-files'] = JsonHelper::toString([
            0 => [
                'file' => \basename($testUploadedFilePath),
                'index' => 0,
                'name' => \basename($testUploadedFilePath),
                'type' => 'image/png',
                'size' => 3636,
                'data' => [
                    'listProps' => [],
                    'tmpFilePath' => Path::makeRelative($testUploadedFilePath, FilePath::getPublicDir()),
                ],
            ],
        ]);
        $client->request(
            $form->getMethod(),
            $form->getUri(),
            $values,
            $form->getPhpFiles()
        );

        $response = $client->getResponse();
        $content = (string) $response->getContent();
        self::assertSame(500, $response->getStatusCode(), $content);
        self::assertStringContainsString('file extension php is not allowed', $content);
    }
}
