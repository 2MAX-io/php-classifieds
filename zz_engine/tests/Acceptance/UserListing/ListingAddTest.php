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
class ListingAddTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return ['app_user_listing_new'];
    }

    public function testAddSimple(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);

        $client->request('GET', $this->getRouter()->generate('app_user_listing_new'));
        $client->submitForm('Save', [
            'listing[title]' => 'test listing',
            'listing[description]' => 'test listing',
            'listing[category]' => '140',
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_user_listing_edit', $client->getRequest()->attributes->get('_route'));
    }

    public function testAddWithCustomFieldsUpload(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);

        $testUploadedFilePath = FilePath::getTempFileUpload().'/test_file_upload.png';
        \copy(FilePath::getPublicDir().'/static/system/1920x1080.png', $testUploadedFilePath);

        $crawler = $client->request('GET', $this->getRouter()->generate('app_user_listing_new'));
        $submitButton = $crawler->selectButton('Save');
        $form = $submitButton->form([
            'listing[title]' => 'test listing',
            'listing[description]' => 'test listing',
            'listing[category]' => '3', // cars
        ]);
        $values = $form->getPhpValues();
        $values['listing']['customFieldList'][1] = '__custom_field_option_id_4'; // brand
        $values['listing']['customFieldList'][2] = '36000'; // mileage
        $values['listing']['customFieldList'][4] = '__custom_field_option_id_24'; // fuel
        $values['listing']['customFieldList'][5] = '2016'; // year
        $values['listing']['customFieldList'][7] = '__custom_field_option_id_26'; // transmission
        $values['listing']['customFieldList'][3][] = '__custom_field_option_id_17'; // transmission
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
        self::assertEquals(302, $response->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_user_listing_edit', $client->getRequest()->attributes->get('_route'));
    }
}
