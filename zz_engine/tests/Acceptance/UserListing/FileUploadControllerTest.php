<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\UserListing;

use App\Helper\FilePath;
use App\Helper\JsonHelper;
use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @internal
 */
class FileUploadControllerTest extends AppIntegrationTestCase
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public function test(): void
    {
        $client = static::createClient();
        $this->clearDatabase();

        $client->request('POST', $this->getRouter()->generate('app_file_upload'), [], [
            'files' => [
                new UploadedFile(FilePath::getProjectDir().'/static/system/1920x1080.png', 'test-file-upload.png'),
            ],
        ]);
        self::assertSame(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
        $responseArray = JsonHelper::toArray($client->getResponse()->getContent() ?: null);
        self::assertTrue($responseArray['isSuccess']);
        self::assertFalse($responseArray['hasWarnings']);
        self::assertCount(1, $responseArray['files']);
        self::assertSame('test-file-upload.png', $responseArray['files'][0]['old_name']);
        self::assertFileExists($responseArray['files'][0]['file']);
    }
}
