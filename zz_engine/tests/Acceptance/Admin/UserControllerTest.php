<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Admin;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Enum\TestDataEnum;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;

/**
 * @internal
 */
class UserControllerTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;
    public const NEW_PASSWORD = 'testnewpassword';

    public static function getRouteNames(): array
    {
        return [
            'app_admin_user_edit',
        ];
    }

    public function testChangePassword(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        // submit form
        $client->request('GET', $this->getRouter()->generate('app_admin_user_edit', [
            'id' => 1,
        ]));
        $client->submitForm('Update', [
            'admin_user_edit[plainPassword][first]' => static::NEW_PASSWORD,
            'admin_user_edit[plainPassword][second]' => static::NEW_PASSWORD,
        ]);
        $response = $client->getResponse();
        self::assertSame(302, $response->getStatusCode());

        // follow redirect after submit
        $client->followRedirect();
        self::assertSame('app_admin_user_edit', $client->getRequest()->attributes->get('_route'));

        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->request('GET', $this->getRouter()->generate('app_login'));
        $client->submitForm('Sign in', [
            'email' => TestDataEnum::LOGIN,
            'password' => static::NEW_PASSWORD,
        ]);
        self::assertSame(302, $response->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_user_listing_new', $client->getRequest()->attributes->get('_route'));
    }

    public function testDisabledUserCannotLogin(): void
    {
        $client = static::createClient();
        $this->clearDatabase();

        // login
        $client->request('GET', $this->getRouter()->generate('app_login'));
        $client->submitForm('Sign in', [
            'email' => TestDataEnum::LOGIN,
            'password' => TestDataEnum::PASSWORD,
        ]);
        self::assertSame(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_user_listing_new', $client->getRequest()->attributes->get('_route'));

        // disable
        $this->loginAdmin($client);
        $crawler = $client->request('GET', $this->getRouter()->generate('app_admin_user_edit', [
            'id' => 1,
        ]));
        $submitButton = $crawler->selectButton('Update');
        $form = $submitButton->form();
        /** @var ChoiceFormField $enabled */
        $enabled = $form['admin_user_edit[enabled]'];
        $enabled->untick();
        $client->submit($form);
        $response = $client->getResponse();
        self::assertSame(302, $response->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_admin_user_edit', $client->getRequest()->attributes->get('_route'));

        // login after account disabled
        static::ensureKernelShutdown();
        $client = static::createClient();
        $client->request('GET', $this->getRouter()->generate('app_login'));
        $client->submitForm('Sign in', [
            'email' => TestDataEnum::LOGIN,
            'password' => TestDataEnum::PASSWORD,
        ]);
        self::assertSame(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_login', $client->getRequest()->attributes->get('_route'));
        self::assertStringContainsString('Account is disabled', (string) $client->getResponse()->getContent());
    }

    public function testListSearch(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $client->request('GET', $this->getRouter()->generate('app_admin_user_list'));
        self::assertSame(200, $client->getResponse()->getStatusCode());
        self::assertStringContainsString(TestDataEnum::LOGIN, (string) $client->getResponse()->getContent());

        // should not find
        $client->request('GET', $this->getRouter()->generate('app_admin_user_list', [
            'query' => 'should not find any',
        ]));
        self::assertSame(200, $client->getResponse()->getStatusCode());
        self::assertStringContainsString('no records found', (string) $client->getResponse()->getContent());

        // should find
        $client->request('GET', $this->getRouter()->generate('app_admin_user_list', [
            'query' => TestDataEnum::LOGIN,
        ]));
        self::assertSame(200, $client->getResponse()->getStatusCode());
        self::assertStringContainsString(TestDataEnum::LOGIN, (string) $client->getResponse()->getContent());
    }
}
