<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\UserListing;

use App\Enum\ParamEnum;
use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Enum\TestUserLoginEnum;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 */
class UserObservedListingActionControllerTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return [
            'app_user_observed_listings',
            'app_user_observed_listing_set',
        ];
    }

    public function test(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client, TestUserLoginEnum::LOGIN2);

        // check not on observed list
        $client->request('GET', $this->getRouter()->generate('app_user_observed_listings'));
        self::assertSame(200, $client->getResponse()->getStatusCode());
        self::assertStringNotContainsString('Test listing title', (string) $client->getResponse()->getContent());

        // set listing observed
        $client->request('POST', $this->getRouter()->generate('app_user_observed_listing_set'), [
            ParamEnum::LISTING_ID => 1,
            ParamEnum::OBSERVED => 1,
        ]);

        // check on observed list
        $client->request('GET', $this->getRouter()->generate('app_user_observed_listings'));
        self::assertSame(200, $client->getResponse()->getStatusCode());
        self::assertStringContainsString('Test listing title', (string) $client->getResponse()->getContent());

        // remove observed
        $client->request('POST', $this->getRouter()->generate('app_user_observed_listing_set'), [
            ParamEnum::LISTING_ID => 1,
            ParamEnum::OBSERVED => 0,
        ]);

        // check not on observed list
        $client->request('GET', $this->getRouter()->generate('app_user_observed_listings'));
        self::assertSame(200, $client->getResponse()->getStatusCode());
        self::assertStringNotContainsString('Test listing title', (string) $client->getResponse()->getContent());
    }
}
