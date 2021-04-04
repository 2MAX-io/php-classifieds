<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Account;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @internal
 */
class RegisterAccountTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;
    public const LOGIN = 'test@example.com';
    public const PASSWORD = 'testpass';

    public static function getRouteNames(): array
    {
        return ['app_register_confirm'];
    }

    public function testUserRegister(): void
    {
        $client = static::createClient();
        $this->clearDatabase();

        // register
        $client->request('GET', $this->getRouter()->generate('app_register'));
        $client->submitForm('Register', [
            'register[email]' => static::LOGIN,
            'register[password][first]' => static::PASSWORD,
            'register[password][second]' => static::PASSWORD,
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode());

        // get confirmation link from email message
        /** @var TemplatedEmail $message */
        $message = $this->getTestContainer()->get('mailer.logger_message_listener')->getEvents()->getMessages()[0];
        $emailCrawler = new Crawler((string) $message->getHtmlBody());
        $confirmUrl = $emailCrawler->selectLink('I confirm registration')->link()->getUri();

        // follow redirect after register submit
        $client->followRedirect();
        self::assertStringContainsString(
            'To finish registration, click confirmation link that you will receive in your email',
            $client->getResponse()->getContent() ?: '',
        );

        // click confirm link
        $client->request('GET', $confirmUrl);
        $client->followRedirect();
        $client->followRedirect();
        self::assertStringContainsString(
            'You have been successfully registered. Now you can add some listings',
            $client->getResponse()->getContent() ?: '',
        );

        // login
        $client->submitForm('Sign in', [
            'email' => static::LOGIN,
            'password' => static::PASSWORD,
        ]);
        self::assertEquals(302, $response->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_user_listing_new', $client->getRequest()->attributes->get('_route'));
    }
}
