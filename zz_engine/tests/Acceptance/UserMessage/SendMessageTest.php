<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\UserMessage;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Enum\TestUserLoginEnum;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 */
class SendMessageTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

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
        $this->loginUser($client);

        // submit form
        $client->request('GET', $this->getRouter()->generate('app_user_message_list_thread', [
            'userMessageThread' => 1,
        ]));
        $client->submitForm('send-message-button', [
            'send_user_message[message]' => 'test user message',
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());

        // follow redirect after submit
        $client->followRedirect();
        self::assertSame('app_user_message_list_thread', $client->getRequest()->attributes->get('_route'));

        $this->loginAdmin($client);
        $client->request('GET', $this->getRouter()->generate('app_admin_police_log_user_message', [
            'user' => 1,
        ]));
        self::assertEquals(200, $client->getResponse()->getStatusCode(), (string) $client->getResponse()->getContent());
    }

    public function testRespondToListing(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client, TestUserLoginEnum::LOGIN2);

        // submit form
        $client->request('GET', $this->getRouter()->generate('app_user_message_respond_to_listing', [
            'listing' => 2,
        ]));
        $client->submitForm('send-message-button', [
            'send_user_message[message]' => 'test user message',
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());

        // follow redirect after submit
        $client->followRedirect();
        self::assertSame('app_user_message_list_thread', $client->getRequest()->attributes->get('_route'));
    }
}
