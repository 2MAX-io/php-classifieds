<?php

declare(strict_types=1);

namespace App\Tests\Smoke\Admin;

use App\Controller\Admin\Category\CustomFieldController;
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
class CustomFieldControllerTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return [
            'app_admin_custom_field_save_order',
        ];
    }

    public function testSaveOrder(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $url = $this->getRouter()->generate('app_admin_custom_field_save_order');
        $requestContent = <<<'EOT'
{"orderedIdList":["2","1","9","3","4","5","8","6","7","25","24","23","22","21","20","19","18","17","16","15","14","13","12","11","10"]}
EOT;
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken(CustomFieldController::CSRF_CUSTOM_FIELDS_SAVE_ORDER);
        $client->request('POST', $url, [], [], [
            'HTTP_'.ParamEnum::CSRF_HEADER => $csrfToken->getValue(),
        ], $requestContent);
        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }
}
