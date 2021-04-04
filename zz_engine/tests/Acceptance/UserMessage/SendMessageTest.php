<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\UserMessage;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Enum\TestUserLoginEnum;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\MessengerTestTrait;
use App\Tests\Traits\RouterTestTrait;
use Carbon\Carbon;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

/**
 * @internal
 */
class SendMessageTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;
    use MessengerTestTrait;

    public static function getRouteNames(): array
    {
        return [
            'app_user_message_list_thread',
            'app_user_message_respond_to_listing',
        ];
    }

    public function testSendMessageToThread(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $senderEmail = TestUserLoginEnum::LOGIN;
        $this->loginUser($client, $senderEmail);

        // submit form
        $client->request('GET', $this->getRouter()->generate('app_user_message_list_thread', [
            'userMessageThread' => 1,
        ]));
        $client->submitForm('send-message-button', [
            'send_user_message[message]' => 'test user message',
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode());

        // follow redirect after submit
        $client->followRedirect();
        self::assertSame('app_user_message_list_thread', $client->getRequest()->attributes->get('_route'));

        // admin has message log
        $this->loginAdmin($client);
        $client->request('GET', $this->getRouter()->generate('app_admin_police_log_user_message', [
            'user' => 1,
        ]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        // test messages send
        Carbon::setTestNow(Carbon::now()->addMinutes(10));
        $this->processMessenger('async');
        $this->assertNoFailedMessages();
        $this->assertMessageQueueEmpty();

        // message send to correct user
        /** @var TemplatedEmail $message */
        $message = $this->getTestContainer()->get('mailer.logger_message_listener')->getEvents()->getMessages()[0];
        self::assertSame(TestUserLoginEnum::LOGIN2, $message->getTo()[0]->getAddress());
        self::assertNotSame($senderEmail, $message->getTo()[0]->getAddress());
    }

    public function testRespondToListing(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $senderEmail = TestUserLoginEnum::LOGIN2;
        $this->loginUser($client, $senderEmail);

        // submit form
        $client->request('GET', $this->getRouter()->generate('app_user_message_respond_to_listing', [
            'listing' => 2,
        ]));
        $client->submitForm('send-message-button', [
            'send_user_message[message]' => 'test user message',
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode());

        // follow redirect after submit
        $client->followRedirect();
        self::assertSame('app_user_message_list_thread', $client->getRequest()->attributes->get('_route'));

        // test messages send
        Carbon::setTestNow(Carbon::now()->addMinutes(10));
        $this->processMessenger('async');
        $this->assertNoFailedMessages();
        $this->assertMessageQueueEmpty();

        // message send to correct user
        /** @var TemplatedEmail $message */
        $message = $this->getTestContainer()->get('mailer.logger_message_listener')->getEvents()->getMessages()[0];
        self::assertSame(TestUserLoginEnum::LOGIN, $message->getTo()[0]->getAddress());
        self::assertNotSame($senderEmail, $message->getTo()[0]->getAddress());
    }
}
