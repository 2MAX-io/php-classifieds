<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Admin;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Enum\TestUserLoginEnum;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;

/**
 * @internal
 */
class AdministratorEditCreateTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;
    public const NEW_LOGIN = 'test-new-administrator@example.com';
    public const NEW_PASSWORD = 'testnewpassword';

    public static function getRouteNames(): array
    {
        return [
            'app_admin_administrator_edit',
            'app_admin_administrator_new',
            'app_admin_login',
        ];
    }

    public function testAdminChangePassword(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $client->request('GET', $this->getRouter()->generate('app_admin_administrator_edit', [
            'id' => 1,
        ]));
        $client->submitForm('Update', [
            'administrator_edit[plainPassword][first]' => static::NEW_PASSWORD,
            'administrator_edit[plainPassword][second]' => static::NEW_PASSWORD,
        ]);
        $response = $client->getResponse();
        self::assertSame(302, $response->getStatusCode());

        // follow redirect after submit
        $client->followRedirect();

        // login
        $client->request('GET', $this->getRouter()->generate('app_admin_login'));
        $client->submitForm('Sign in', [
            'email' => TestUserLoginEnum::LOGIN_ADMIN,
            'password' => static::NEW_PASSWORD,
        ]);
        self::assertSame(302, $response->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_admin_index', $client->getRequest()->attributes->get('_route'));
    }

    public function testNewAdmin(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $client->request('GET', $this->getRouter()->generate('app_admin_administrator_new', [
            'id' => 1,
        ]));
        $client->submitForm('Save', [
            'administrator_new[email][first]' => static::NEW_LOGIN,
            'administrator_new[email][second]' => static::NEW_LOGIN,
            'administrator_new[plainPassword][first]' => static::NEW_PASSWORD,
            'administrator_new[plainPassword][second]' => static::NEW_PASSWORD,
        ]);
        $response = $client->getResponse();
        self::assertSame(302, $response->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_admin_administrator_edit', $client->getRequest()->attributes->get('_route'));

        // login
        $client->request('GET', $this->getRouter()->generate('app_admin_login'));
        $client->submitForm('Sign in', [
            'email' => static::NEW_LOGIN,
            'password' => static::NEW_PASSWORD,
        ]);
        self::assertSame(302, $response->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_admin_index', $client->getRequest()->attributes->get('_route'));
    }

    public function testDisabledAdminCannotLogin(): void
    {
        $client = static::createClient();
        $this->clearDatabase();

        // login
        $client->request('GET', $this->getRouter()->generate('app_admin_login'));
        $client->submitForm('Sign in', [
            'email' => TestUserLoginEnum::LOGIN_ADMIN,
            'password' => TestUserLoginEnum::PASSWORD,
        ]);
        self::assertSame(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_admin_index', $client->getRequest()->attributes->get('_route'));

        // disable
        $crawler = $client->request('GET', $this->getRouter()->generate('app_admin_administrator_edit', [
            'id' => 1,
        ]));
        $submitButton = $crawler->selectButton('Update');
        $form = $submitButton->form();
        /** @var ChoiceFormField $enabled */
        $enabled = $form['administrator_edit[enabled]'];
        $enabled->untick();
        $client->submit($form);
        $response = $client->getResponse();
        self::assertSame(302, $response->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_admin_administrator_edit', $client->getRequest()->attributes->get('_route'));

        // login after account disabled
        static::ensureKernelShutdown();
        $client = static::createClient();
        $client->request('GET', $this->getRouter()->generate('app_admin_login'));
        $client->submitForm('Sign in', [
            'email' => TestUserLoginEnum::LOGIN_ADMIN,
            'password' => TestUserLoginEnum::PASSWORD,
        ]);
        self::assertSame(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_admin_login', $client->getRequest()->attributes->get('_route'));
        self::assertStringContainsString('Account is disabled', (string) $client->getResponse()->getContent());
    }
}
