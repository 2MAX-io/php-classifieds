<?php

declare(strict_types=1);

namespace App\Tests\Smoke\Admin;

use App\Controller\Admin\Category\AdminCategoryController;
use App\Enum\ParamEnum;
use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;
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
            'app_admin_category_delete',
            'app_admin_category_save_order',
        ];
    }

    public function testDeleteCategory(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);
        $this->executeSql("INSERT INTO category (id, parent_id, name, lvl, lft, rgt, sort, slug, advertisement_zone_id, picture) VALUES (143, 1, 'category to delete', 1, 2, 3, 240, 'category-to-delete', null, null);");

        $id = 143;
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_deleteCategory'.$id);
        $url = $this->getRouter()->generate('app_admin_category_delete', [
            'id' => $id,
        ]);
        $client->request('DELETE', $url, [
            '_token' => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();

        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());
        self::assertEquals('/admin/red5/category', $client->getResponse()->headers->get('location'));
    }

    public function testSaveOrder(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);
        $this->executeSql("INSERT INTO custom_field (id, name, name_for_admin, type, required, searchable, inline_on_list, sort, unit) VALUES (26, 'custom field to delete', 'custom field to delete', 'SELECT_AS_CHECKBOXES', 0, 1, 1, 126, null);");

        $url = $this->getRouter()->generate('app_admin_category_save_order');
        $requestContent = <<<'EOT'
{"orderedIdList":["1","9","2","3","4","5","8","6","7","25","24","23","22","21","20","19","18","17","16","15","14","13","12","11","26","10"]}
EOT;
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken(AdminCategoryController::CSRF_CATEGORY_SORT_SAVE);
        $client->request('POST', $url, [], [], [
            'HTTP_'.ParamEnum::CSRF_HEADER => $csrfToken->getValue(),
        ], $requestContent);
        $response = $client->getResponse();

        self::assertEquals(200, $response->getStatusCode(), (string) $response->getContent());
    }
}
