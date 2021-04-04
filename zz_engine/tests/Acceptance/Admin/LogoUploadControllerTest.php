<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Admin;

use App\Helper\FilePath;
use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;
use Symfony\Component\DomCrawler\Field\FileFormField;

/**
 * @internal
 */
class LogoUploadControllerTest extends AppIntegrationTestCase
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public function testAddCategory(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $crawler = $client->request('GET', $this->getRouter()->generate('app_admin_logo_upload'));
        $buttonCrawlerNode = $crawler->selectButton('Upload');
        $form = $buttonCrawlerNode->form();
        /** @var FileFormField $pictureField */
        $pictureField = $form['admin_logo_upload[logo]'];
        $pictureField->upload(FilePath::getProjectDir().'/static/system/1920x1080.png');
        $client->submit($form);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_admin_logo_upload', $client->getRequest()->attributes->get('_route'));
    }
}
