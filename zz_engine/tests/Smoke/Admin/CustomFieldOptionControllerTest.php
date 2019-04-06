<?php

declare(strict_types=1);

namespace App\Tests\Smoke\Admin;

use App\Controller\Admin\Category\CustomFieldOptionController;
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
class CustomFieldOptionControllerTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return [
            'app_admin_custom_field_options_save_order',
        ];
    }

    public function testSaveOrder(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $url = $this->getRouter()->generate('app_admin_custom_field_options_save_order');
        $requestContent = <<<'EOT'
{"orderedIdList":["3","2","4","5","6","7","8","9","10","11","12","13","14","15","16","1"]}
EOT;
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken(CustomFieldOptionController::CSRF_SAVE_ORDER);
        $client->request('POST', $url, [], [], [
            'HTTP_'.ParamEnum::CSRF_HEADER => $csrfToken->getValue(),
        ], $requestContent);
        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }
}
