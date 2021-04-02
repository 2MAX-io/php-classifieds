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
class RemindPasswordTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return ['app_remind_password_confirm'];
    }

    public function testRemindPassword(): void
    {
        $client = static::createClient();
        $this->clearDatabase();

        // remind password
        $client->request('GET', $this->getRouter()->generate('app_remind_password'));
        $client->submitForm('Remind password', [
            'remind_password[email]' => TestUserLoginEnum::LOGIN,
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());

        // get confirmation link from email message
        /** @var TemplatedEmail $message */
        $message = $this->getTestContainer()->get('mailer.logger_message_listener')->getEvents()->getMessages()[0];
        $emailCrawler = new Crawler((string) $message->getHtmlBody());
        $confirmUrl = $emailCrawler->selectLink('I confirm password reset')->link()->getUri();
        $newPassword = $message->getContext()['plainPassword'];

        // follow redirect after remind password submit
        $client->followRedirect();
        self::assertStringContainsString(
            'To remind password, please click confirmation link that you would receive on your email address',
            $client->getResponse()->getContent() ?: '',
        );

        // click confirm link
        $client->request('GET', $confirmUrl);
        $client->followRedirect();
        self::assertStringContainsString(
            'Password reset has been successful',
            $client->getResponse()->getContent() ?: '',
        );

        // login
        $client->submitForm('Sign in', [
            'email' => TestUserLoginEnum::LOGIN,
            'password' => $newPassword,
        ]);
        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());
        $client->followRedirect();
        self::assertSame('app_user_listing_new', $client->getRequest()->attributes->get('_route'));
    }
}
