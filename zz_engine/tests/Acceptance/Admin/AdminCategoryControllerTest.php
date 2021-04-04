<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Admin;

use App\Helper\FilePath;
use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;
use Symfony\Component\DomCrawler\Field\FileFormField;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @internal
 */
class AdminCategoryControllerTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return [
            'app_admin_category_new',
            'app_admin_category_edit',
        ];
    }

    public function testAddCategory(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $crawler = $client->request('GET', $this->getRouter()->generate('app_admin_category_new'));
        $buttonCrawlerNode = $crawler->selectButton('Save');
        $form = $buttonCrawlerNode->form([
            'admin_category_save[name]' => 'test cat edit',
            'admin_category_save[slug]' => 'test-cat-slug',
            'admin_category_save[parent]' => '2',
            'admin_category_save[sort]' => '101',
        ]);
        /** @var FileFormField $pictureField */
        $pictureField = $form['admin_category_save[picture]'];
        $pictureField->upload(FilePath::getProjectDir().'/static/system/1920x1080.png');
        $client->submit($form);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());
        $client->followRedirect();
        self::assertSame('app_admin_category_edit', $client->getRequest()->attributes->get('_route'));
    }

    public function testEditCategory(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $client->request('GET', $this->getRouter()->generate('app_admin_category_edit', [
            'id' => 2,
        ]));
        $client->submitForm('Update', [
            'admin_category_save[name]' => 'test cat edit',
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());
        $client->followRedirect();
        self::assertSame('app_admin_category_edit', $client->getRequest()->attributes->get('_route'));
    }

    public function testDeleteCategoryWithListings(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $id = 140;
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_deleteCategory'.$id);
        $url = $this->getRouter()->generate('app_admin_category_delete', [
            'id' => $id,
        ]);
        $client->request('DELETE', $url, [
            '_token' => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();

        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());
        $client->followRedirect();
        self::assertEquals('app_admin_category_edit', $client->getRequest()->get('_route'));
        self::assertStringContainsString('To delete category, you must first delete or move all dependencies like', (string) $client->getResponse()->getContent());
    }
}
