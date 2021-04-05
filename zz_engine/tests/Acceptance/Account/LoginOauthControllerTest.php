<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Account;

use App\Controller\User\Account\LoginOauthController;
use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Enum\TestUserLoginEnum;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;
use Hybridauth\Adapter\AdapterInterface;
use Hybridauth\Hybridauth;
use Hybridauth\User\Profile;

/**
 * @internal
 */
class LoginOauthControllerTest extends AppIntegrationTestCase
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public function test(): void
    {
        $client = static::createClient();
        $this->clearDatabase();

        $hybridauthStub = $this->createMock(Hybridauth::class);
        $hybridauthStub->method('authenticate')->willReturnCallback(function () {
            $authenticationStub = $this->createMock(AdapterInterface::class);
            $authenticationStub->method('isConnected')->willReturn(true);
            $authenticationStub->method('getUserProfile')->willReturnCallback(function () {
                $profile = new Profile();
                $profile->emailVerified = TestUserLoginEnum::LOGIN;

                return $profile;
            });

            return $authenticationStub;
        });
        $this->getTestContainer()->get(LoginOauthController::class)->setHybridauth($hybridauthStub);

        $client->request('GET', $this->getRouter()->generate('app_login_oauth', [
            'provider' => LoginOauthController::GOOGLE_PROVIDER,
        ]));
        $client->followRedirect();
        self::assertSame('app_user_listing_new', $client->getRequest()->attributes->get('_route'));
        $client->request('GET', $this->getRouter()->generate('app_user_my_account'));
        self::assertSame(200, $client->getResponse()->getStatusCode());
    }
}
