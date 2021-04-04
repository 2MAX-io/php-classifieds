<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Security;

use App\Security\Encoder\PhpassPasswordEncoderService;
use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Enum\TestUserLoginEnum;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @internal
 */
class PhpassPasswordEncoderServiceTest extends AppIntegrationTestCase
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public function test(): void
    {
        $client = static::createClient();
        $this->clearDatabase();

        // set phpass password
        $password = 'test-phpass-password';
        $phpassPasswordEncoderService = self::$container->get(PhpassPasswordEncoderService::class);
        $pdo = $this->getTestContainer()->get(EntityManagerInterface::class)->getConnection();
        $pdo->executeQuery('UPDATE user SET password = :password WHERE email = :email', [
            'password' => $phpassPasswordEncoderService->encodePassword($password, null),
            'email' => TestUserLoginEnum::LOGIN,
        ]);

        // login
        $client->request('GET', $this->getRouter()->generate('app_login'));
        $client->submitForm('Sign in', [
            'email' => TestUserLoginEnum::LOGIN,
            'password' => $password,
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_user_listing_new', $client->getRequest()->attributes->get('_route'));
    }
}
