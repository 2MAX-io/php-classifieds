<?php

declare(strict_types=1);

namespace App\Tests\Smoke\Admin;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @internal
 */
class AdminActionTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return [
            'app_admin_listing_activate',
            'app_admin_listing_remove',
            'app_admin_listing_pull_up',
            'app_admin_listing_feature_for_week',
            'app_admin_listing_file_remove',
            'app_admin_listing_redirect_next_waiting_activation',
            'app_admin_package_delete',
            'app_admin_listing_report_remove',
            'app_admin_custom_field_option_delete',
            'app_admin_custom_field_delete',
            'app_admin_category_custom_field_for_category_delete',
            'app_admin_page_delete',
        ];
    }

    public function testActivate(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $listingId = 1;
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_adminActivateListing'.$listingId);
        $url = $this->getRouter()->generate('app_admin_listing_activate', [
            'id' => $listingId,
        ]);
        $client->request('PATCH', $url, [
            '_token' => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();

        self::assertSame(302, $response->getStatusCode());
        self::assertSame('/', $client->getResponse()->headers->get('location'));
    }

    public function testRemove(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $listingId = 1;
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_adminRemoveListing'.$listingId);
        $url = $this->getRouter()->generate('app_admin_listing_remove', [
            'id' => $listingId,
        ]);
        $client->request('DELETE', $url, [
            '_token' => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();

        self::assertSame(302, $response->getStatusCode());
        self::assertSame('/', $client->getResponse()->headers->get('location'));

        // check listing displayed correctly
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->request('GET', $this->getRouter()->generate('app_listing_show', [
            'id' => $listingId,
            'slug' => 'test-listing-title',
        ]));
        self::assertStringContainsString('Listing has been removed by Administrator', (string) $client->getResponse()->getContent());
        self::assertStringNotContainsString('Show contact information', (string) $client->getResponse()->getContent());
    }

    public function testPullUp(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $listingId = 1;
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_adminPullUpListing'.$listingId);
        $url = $this->getRouter()->generate('app_admin_listing_pull_up', [
            'id' => $listingId,
        ]);
        $client->request('PATCH', $url, [
            '_token' => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();

        self::assertSame(302, $response->getStatusCode());
    }

    public function testFeatureForWeek(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $listingId = 1;
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_adminFeatureForWeekListing'.$listingId);
        $url = $this->getRouter()->generate('app_admin_listing_feature_for_week', [
            'id' => $listingId,
        ]);
        $client->request('PATCH', $url, [
            '_token' => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();

        self::assertSame(302, $response->getStatusCode());
        self::assertSame('/', $client->getResponse()->headers->get('location'));
    }

    public function testListingFileRemove(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $id = 1;
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_adminRemoveListingFile'.$id);
        $url = $this->getRouter()->generate('app_admin_listing_file_remove', [
            'id' => $id,
        ]);
        $client->request('DELETE', $url, [
            '_token' => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();

        self::assertSame(302, $response->getStatusCode());
        self::assertSame('/admin/red5/listing/edit/1', $client->getResponse()->headers->get('location'));
    }

    public function testFeaturePackageDelete(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $id = 1;
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_deletePackage'.$id);
        $url = $this->getRouter()->generate('app_admin_package_delete', [
            'id' => $id,
        ]);
        $client->request('DELETE', $url, [
            '_token' => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();

        self::assertSame(302, $response->getStatusCode());
        self::assertSame('/admin/red5/package', $client->getResponse()->headers->get('location'));
    }

    public function testListingReportRemove(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);
        $this->executeSql("INSERT INTO zzzz_listing_report (id, listing_id, user_id, datetime, report_message, email, removed, police_log) VALUES (1, 1, 1, '2021-04-01 04:08:53', 'test report', 'user-demo@2max.io', 0, '2021-04-01 04:08:53.395500 +00:00: 192.168.205.1:55698 --> 192.168.205.3:443');");

        $id = 1;
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_listingReportRemove'.$id);
        $url = $this->getRouter()->generate('app_admin_listing_report_remove', [
            'id' => $id,
        ]);
        $client->request('DELETE', $url, [
            '_token' => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();

        self::assertSame(302, $response->getStatusCode());
        self::assertSame('/admin/red5/listing-report', $client->getResponse()->headers->get('location'));
    }

    public function testCustomFieldOptionDelete(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $id = 2;
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_deleteFieldOption'.$id);
        $url = $this->getRouter()->generate('app_admin_custom_field_option_delete', [
            'id' => $id,
        ]);
        $client->request('DELETE', $url, [
            '_token' => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();

        self::assertSame(302, $response->getStatusCode());
        self::assertSame('/admin/red5/custom-field/1/edit', $client->getResponse()->headers->get('location'));
    }

    public function testCustomFieldDelete(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);
        $this->executeSql("INSERT INTO custom_field (id, name, name_for_admin, type, required, searchable, inline_on_list, sort, unit) VALUES (26, 'custom field to delete', 'custom field to delete', 'SELECT_AS_CHECKBOXES', 0, 1, 1, 126, null);");

        $id = 26;
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_deleteCustomField'.$id);
        $url = $this->getRouter()->generate('app_admin_custom_field_delete', [
            'id' => $id,
        ]);
        $client->request('DELETE', $url, [
            '_token' => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();

        self::assertSame(302, $response->getStatusCode());
        self::assertSame('/admin/red5/custom-field/list', $client->getResponse()->headers->get('location'));
    }

    public function testCustomFieldForCategoryDelete(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $id = 1;
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_deleteCustomFieldForCategory'.$id);
        $url = $this->getRouter()->generate('app_admin_category_custom_field_for_category_delete', [
            'id' => $id,
        ]);
        $client->request('DELETE', $url, [
            '_token' => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();

        self::assertSame(302, $response->getStatusCode());
        self::assertSame('/admin/red5/category/2/edit', $client->getResponse()->headers->get('location'));
    }

    public function testPageDelete(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $id = 1;
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_deletePage'.$id);
        $url = $this->getRouter()->generate('app_admin_page_delete', [
            'id' => $id,
        ]);
        $client->request('DELETE', $url, [
            '_token' => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();

        self::assertSame(302, $response->getStatusCode());
        self::assertSame('/admin/red5/page', $client->getResponse()->headers->get('location'));
    }

    public function testRedirectToNextListingWaitingActivation(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_adminRedirectNextWaitingActivation');
        $url = $this->getRouter()->generate('app_admin_listing_redirect_next_waiting_activation');
        $client->request('PATCH', $url, [
            '_token' => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();

        self::assertSame(302, $response->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_listing_show', $client->getRequest()->get('_route'));
    }
}
