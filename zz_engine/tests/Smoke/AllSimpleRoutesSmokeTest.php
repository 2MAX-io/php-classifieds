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

    /** @var string[] */
    private static $skipRoutes = [
        'nelmio_js_logger_log',
        'app_logout',
        'app_admin_upgrade_run',
        'app_map_image_cache_template',
        'app_payment_status_refresh',
    ];

    /** @var string[] */
    private static $allowedToSkipRoutes = [
        'fos_js_routing_js',
        'bazinga_jstranslation_js',
        'nelmio_js_logger_log',
        'app_logout',
        'app_login_oauth',
        'app_admin_upgrade_run',
        'app_file_upload',
        'app_map_image_cache',
        'app_map_image_cache_template',
        'app_user_change_email_previous_email_confirmation',
        'app_user_change_password_confirm',
        'app_register_confirm',
        'app_remind_password_confirm',
        'app_payment_notify',
        'app_payment_success',
    ];

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
        $testedRoutes = $this->getTestedRoutes();

        foreach ($this->getRoutes() as $routeName => $route) {
            if (!empty($route->getMethods()) && !\in_array('GET', $route->getMethods(), true)) {
                if (!\in_array($routeName, $testedRoutes, true)) {
                    $this->skippedRoutes[] = $routeName;
                }
                continue;
            }
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
                if (\in_array($routeName, static::$skipRoutes, true)) {
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

        $this->skippedRoutes = \array_diff(
            $this->skippedRoutes,
            $testedRoutes,
            static::$allowedToSkipRoutes,
        );
        \sort($this->skippedRoutes);
        self::assertLessThanOrEqual(
            0,
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
        $urlList[] = $this->getRouter()->generate('app_user_message_list_thread', [
            'userMessageThread' => 1,
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
                /** @noinspection SlowArrayOperationsInLoopInspection */
                $routes = \array_merge($routes, $className::getRouteNames());
            }
        }

        return $routes;
    }
}
