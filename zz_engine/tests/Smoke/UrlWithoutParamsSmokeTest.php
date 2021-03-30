<?php

declare(strict_types=1);

namespace App\Tests\Smoke;

use App\Tests\Base\LoginTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * @internal
 * @coversNothing
 */
class UrlWithoutParamsSmokeTest extends WebTestCase
{
    use LoginTrait;

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
                        'app_admin_listing_search',
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
                "failed for route: `{$routeName}`, url: {$url}",
            );
        }
    }

    /**
     * @return Route[]
     */
    protected function getRoutes(): array
    {
        return $this->getRouter()->getRouteCollection()->all();
    }

    protected function getRouter(): RouterInterface
    {
        return static::$kernel->getContainer()->get('router');
    }

    /**
     * @return array<string, mixed>
     */
    private function generateConfigForRouteList(): array
    {
        $this->configForRouteList['app_listing_show'] = [
            'url' => $this->getRouter()->generate(
                'app_listing_show', ['id' => 1, 'slug' => 'at-sit-aliquam-reprehenderit']
            ),
        ];

        return $this->configForRouteList;
    }
}
