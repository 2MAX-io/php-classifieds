<?php

declare(strict_types=1);

namespace App\Tests\Smoke;

use App\Controller\Pub\ListingShowController;
use App\Enum\ParamEnum;
use App\Helper\JsonHelper;
use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\RouterTestTrait;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @internal
 */
class ListingShowControllerTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;

    public static function getRouteNames(): array
    {
        return [
            'app_listing_contact_data',
        ];
    }

    public function testShowContractInformation(): void
    {
        $client = static::createClient();
        $this->clearDatabase();

        $id = 1;
        $url = $this->getRouter()->generate('app_listing_contact_data');
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken(ListingShowController::CSRF_SHOW_CONTACT_DATA.$id);
        $client->request('POST', $url, [
            ParamEnum::LISTING_ID => $id,
        ], [], [
            'HTTP_'.ParamEnum::CSRF_HEADER => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();

        $responseContent = (string) $response->getContent();
        self::assertEquals(200, $response->getStatusCode(), $responseContent);
        self::assertArrayHasKey(ParamEnum::SHOW_CONTACT_HTML, JsonHelper::toArray($responseContent));
    }
}
