<?php

declare(strict_types=1);

namespace App\Tests\Smoke;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;
use Symfony\Component\Routing\Route;

/**
 * @internal
 * @coversNothing
 */
class AllSimpleRoutesSmokeTest extends AppIntegrationTestCase
{
    use LoginTestTrait;
    use DatabaseTestTrait;
    use RouterTestTrait;

    /**
     * @var string[]
     */
    private $skippedRoutes = [];

    /**
     * @var array<string, mixed>
     */
    private $configForRouteList;

    public function testUrls(): void
    {
        $client = static::createClient();
        $GLOBALS['kernel'] = static::$kernel;
        $this->clearDatabase();

        $this->generateConfigForRouteList();

        foreach ($this->getRoutes() as $routeName => $route) {
            if (\str_starts_with($routeName, 'app_admin_')) {
                $this->loginAdmin($client);
            }
            if (\str_starts_with($route->getPath(), '/user/')) {
                $this->loginUser($client);
            }

            $hasRouteConfig = isset($this->configForRouteList[$routeName]);
            if ($hasRouteConfig) {
                $url = $this->configForRouteList[$routeName]['url'];
            } else {
                if (\in_array(
                    $routeName, [
                        'app_listing_contact_data',
                        'app_file_upload',
                        'app_map_image_cache_template',
                        'app_listing_get_custom_fields',
                        'app_logout',
                        'nelmio_js_logger_log',
                        'app_admin_listing_redirect_next_waiting_activation',
                        'app_admin_listing_activate_action_on_selected',
                        'app_admin_category_save_order',
                        'app_admin_category_custom_fields_save_order',
                        'app_admin_custom_field_save_order',
                        'app_admin_custom_field_options_save_order',
                        'app_admin_upgrade_run',
                        'app_listing_file_remove',
                        'app_payment_status_refresh',
                    ],
                    true)
                ) {
                    $this->skippedRoutes[] = $routeName;
                    continue;
                }

                if (\str_contains($route->getPath(), '{')) {
                    $this->skippedRoutes[] = (string) $routeName;
                    continue;
                }

                $url = $this->getRouter()->generate((string) $routeName);
            }

            $client->request('GET', $url);
            $response = $client->getResponse();
            self::assertEquals(
                200,
                $response->getStatusCode(),
                "failed for route: `{$routeName}`, url: {$url}\n\n".$response->getContent(),
            );
        }

        $this->skippedRoutes = \array_diff($this->skippedRoutes, $this->getTestedRoutes());
        self::assertLessThanOrEqual(
            50,
            \count($this->skippedRoutes),
            \implode("\n", $this->skippedRoutes),
        );
    }

    /**
     * @return Route[]
     */
    protected function getRoutes(): array
    {
        return $this->getRouter()->getRouteCollection()->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function generateConfigForRouteList(): array
    {
        $urlList = [];
        $urlList[] = $this->getRouter()->generate('app_listing_show', [
            'id' => 1,
            'slug' => 'test-listing-title',
        ]);
        $urlList[] = $this->getRouter()->generate('app_admin_listing_edit', [
            'id' => 1,
        ]);
        $urlList[] = $this->getRouter()->generate('app_admin_listing_edit_advanced', [
            'id' => 1,
        ]);
        $urlList[] = $this->getRouter()->generate('app_admin_listing_edit_advanced', [
            'id' => 1,
        ]);
        $urlList[] = $this->getRouter()->generate('app_admin_user_edit', [
            'id' => 1,
        ]);
        $urlList[] = $this->getRouter()->generate('app_admin_user_change_balance', [
            'id' => 1,
        ]);
        $urlList[] = $this->getRouter()->generate('app_admin_featured_package_edit', [
            'id' => 1,
        ]);
        $urlList[] = $this->getRouter()->generate('app_admin_administrator_edit', [
            'id' => 1,
        ]);
        $urlList[] = $this->getRouter()->generate('app_admin_police_log_listing', [
            'id' => 1,
        ]);
        $urlList[] = $this->getRouter()->generate('app_admin_listing_reject', [
            'id' => 1,
        ]);
        $urlList[] = $this->getRouter()->generate('app_admin_page_edit', [
            'id' => 1,
        ]);
        $urlList[] = $this->getRouter()->generate('app_admin_custom_field_option_copy', [
            'id' => 1,
        ]);
        $urlList[] = $this->getRouter()->generate('app_admin_custom_field_option_edit', [
            'id' => 1,
        ]);
        $urlList[] = $this->getRouter()->generate('app_admin_custom_field_option_add', [
            'id' => 1,
        ]);
        $urlList[] = $this->getRouter()->generate('app_admin_custom_field_edit', [
            'id' => 1,
        ]);
        $urlList[] = $this->getRouter()->generate('app_admin_category_add_custom_field', [
            'id' => 2,
        ]);
        $urlList[] = $this->getRouter()->generate('app_admin_category_edit', [
            'id' => 2,
        ]);
        $urlList[] = $this->getRouter()->generate('app_admin_payment_show', [
            'id' => 1,
        ]);
        $urlList[] = $this->getRouter()->generate('app_admin_invoice_show', [
            'id' => 1,
        ]);
        $urlList[] = $this->getRouter()->generate('app_user_validity_extend', [
            'id' => 1,
        ]);
        $urlList[] = $this->getRouter()->generate('app_user_listing_edit', [
            'id' => 1,
        ]);
        $urlList[] = $this->getRouter()->generate('app_user_feature_listing', [
            'id' => 1,
        ]);
        $urlList[] = $this->getRouter()->generate('app_report_listing', [
            'listing' => 1,
        ]);
        $urlList[] = $this->getRouter()->generate('app_page', [
            'slug' => 'terms-and-conditions',
        ]);
        $urlList[] = $this->getRouter()->generate('app_category', [
            'categorySlug' => 'automotive',
        ]);
        $urlList[] = $this->getRouter()->generate('app_user_message_respond_to_listing', [
            'listing' => 1,
        ]);

        $router = $this->getRouter();
        foreach ($urlList as $url) {
            $routeName = (string) $router->match($url)['_route'];
            $this->configForRouteList[$routeName] = [
                'routeName' => $routeName,
                'url' => $url,
            ];
        }

        return $this->configForRouteList;
    }

    /**
     * @return string[]
     */
    private function getTestedRoutes(): array
    {
        $routes = [];
        foreach (\get_declared_classes() as $className) {
            if (\in_array(
                SmokeTestForRouteInterface::class,
                \class_implements($className) ?: [],
                true,
            )) {
                $routes[] = $className::getRouteName();
            }
        }

        return $routes;
    }
}
