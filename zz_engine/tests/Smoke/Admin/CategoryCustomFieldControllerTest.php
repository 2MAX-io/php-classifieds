<?php

declare(strict_types=1);

namespace App\Tests\Smoke\Admin;

use App\Controller\Admin\Category\CategoryCustomFieldController;
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
class CategoryCustomFieldControllerTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return [
            'app_admin_category_custom_fields_save_order',
        ];
    }

    public function testSaveOrder(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $url = $this->getRouter()->generate('app_admin_category_custom_fields_save_order');
        $requestContent = <<<'EOT'
{"orderedIdList":["15","1","3","7","9"]}
EOT;
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken(CategoryCustomFieldController::CSRF_CUSTOM_FIELDS_FOR_CATEGORY_ORDER_SAVE);
        $client->request('POST', $url, [], [], [
            'HTTP_'.ParamEnum::CSRF_HEADER => $csrfToken->getValue(),
        ], $requestContent);
        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }
}
