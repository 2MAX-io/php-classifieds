<?php

declare(strict_types=1);

namespace App\Tests\Smoke;

use App\Controller\Pub\ListingShowController;
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
class GetCustomFieldsControllerTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return [
            'app_listing_get_custom_fields',
        ];
    }

    public function testGetCustomFields(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);

        $listingId = 1;
        $url = $this->getRouter()->generate('app_listing_get_custom_fields', [
            ParamEnum::LISTING_ID => $listingId,
            ParamEnum::CATEGORY_ID => 3,
        ]);
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken(ListingShowController::CSRF_SHOW_CONTACT_DATA.$listingId);
        $client->request('POST', $url, [], [], [
            'HTTP_'.ParamEnum::CSRF_HEADER => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();

        $responseContent = (string) $response->getContent();
        self::assertEquals(200, $response->getStatusCode(), $responseContent);
        self::assertStringContainsString('listing_customFieldList', $responseContent);
    }
}
