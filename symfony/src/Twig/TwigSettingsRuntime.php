<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\Setting\SettingsDto;
use App\Service\Setting\SettingsService;
use App\System\Cache\CacheService;
use Psr\SimpleCache\CacheInterface;
use Twig\Extension\RuntimeExtensionInterface;

class TwigSettingsRuntime implements RuntimeExtensionInterface
{
    /**
     * @var SettingsService
     */
    private $settingsService;

    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(SettingsService $settingsService, CacheInterface $cache)
    {
        $this->settingsService = $settingsService;
        $this->cache = $cache;
    }

    public function settings(): SettingsDto
    {
        static $cache = null;

        if ($cache) {
            return $cache;
        }

        if ($this->cache->has(CacheService::TWIG_SETTINGS_CACHE)) {
            return $this->cache->get(CacheService::TWIG_SETTINGS_CACHE);
        }

        $settingsDto = $this->settingsService->getHydratedSettingsDto();
        $this->cache->set(CacheService::TWIG_SETTINGS_CACHE, $settingsDto, 300);
        $cache = $settingsDto;

        return $settingsDto;
    }
}
