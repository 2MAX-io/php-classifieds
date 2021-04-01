<?php

declare(strict_types=1);

namespace App\Tests\Smoke;

use App\Enum\ParamEnum;
use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @internal
 * @coversNothing
 */
class ListingFileRemoveControllerTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return [
            'app_user_listing_file_remove',
        ];
    }

    public function testRemoveListingFile(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);

        $listingFileId = 1;
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_listingFileRemove');
        $url = $this->getRouter()->generate('app_user_listing_file_remove');
        $client->request('POST', $url, [
            'listingFileId' => $listingFileId,
        ], [], [
            'HTTP_'.ParamEnum::CSRF_HEADER => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();

        self::assertEquals(200, $response->getStatusCode(), (string) $response->getContent());
    }
}
