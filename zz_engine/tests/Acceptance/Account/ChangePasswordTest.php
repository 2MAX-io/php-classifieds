<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Account;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Enum\TestUserLoginEnum;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @internal
 */
class ChangePasswordTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;
    public const NEW_PASSWORD = 'testnewpassword';

    public static function getRouteNames(): array
    {
        return ['app_user_change_password_confirm'];
    }

    public function testChangePassword(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);

        // change password
        $client->request('GET', $this->getRouter()->generate('app_user_change_password'));
        $client->submitForm('Change Password', [
            'change_password[currentPassword]' => TestUserLoginEnum::PASSWORD,
            'change_password[newPassword][first]' => static::NEW_PASSWORD,
            'change_password[newPassword][second]' => static::NEW_PASSWORD,
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode());

        // get confirmation link from email message
        /** @var TemplatedEmail $message */
        $message = $this->getTestContainer()->get('mailer.logger_message_listener')->getEvents()->getMessages()[0];
        $emailCrawler = new Crawler((string) $message->getHtmlBody());
        $confirmUrl = $emailCrawler->selectLink('I confirm password change')->link()->getUri();

        // follow redirect after submit
        $client->followRedirect();
        self::assertStringContainsString(
            'To finalize password change, open your email account and click confirmation link',
            $client->getResponse()->getContent() ?: '',
        );

        // click confirm link
        $client->request('GET', $confirmUrl);
        $client->followRedirect();
        $client->followRedirect();
        self::assertStringContainsString(
            'Password change has been successful',
            $client->getResponse()->getContent() ?: '',
        );

        // login
        $client->submitForm('Sign in', [
            'email' => TestUserLoginEnum::LOGIN,
            'password' => static::NEW_PASSWORD,
        ]);
        self::assertEquals(302, $response->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_user_change_password', $client->getRequest()->attributes->get('_route'));
    }
}
