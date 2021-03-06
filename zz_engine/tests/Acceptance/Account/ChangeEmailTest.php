<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Account;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Enum\TestDataEnum;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @internal
 */
class ChangeEmailTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;
    public const NEW_EMAIL = 'test-new-email@example.com';

    public static function getRouteNames(): array
    {
        return ['app_user_change_email_previous_email_confirmation'];
    }

    public function testChangeEmail(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);

        // change email
        $client->request('GET', $this->getRouter()->generate('app_user_change_email'));
        $client->submitForm('Change email', [
            'change_email[currentPassword]' => TestDataEnum::PASSWORD,
            'change_email[newEmail][first]' => static::NEW_EMAIL,
            'change_email[newEmail][second]' => static::NEW_EMAIL,
        ]);
        $response = $client->getResponse();
        self::assertSame(302, $response->getStatusCode());

        // get confirmation link from email message
        /** @var TemplatedEmail $email */
        $email = self::getMailerMessage();
        $emailCrawler = new Crawler((string) $email->getHtmlBody());
        $confirmUrl = $emailCrawler->selectLink('I confirm change of email address to: '.static::NEW_EMAIL)->link()->getUri();

        // follow redirect after submit
        $client->followRedirect();
        self::assertStringContainsString(
            'To finalize email change, open your email account and click confirmation link',
            $client->getResponse()->getContent() ?: '',
        );

        // click confirm link
        $client->request('GET', $confirmUrl);
        $client->followRedirect();
        $client->followRedirect();
        self::assertStringContainsString(
            'Email address change has been successful',
            $client->getResponse()->getContent() ?: '',
        );

        // login
        $client->submitForm('Sign in', [
            'email' => static::NEW_EMAIL,
            'password' => TestDataEnum::PASSWORD,
        ]);
        self::assertSame(302, $response->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_user_change_email', $client->getRequest()->attributes->get('_route'));
    }
}
