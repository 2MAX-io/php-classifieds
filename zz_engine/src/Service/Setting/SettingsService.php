<?php

declare(strict_types=1);

namespace App\Service\Setting;

use App\Entity\System\Setting;
use App\Enum\AppCacheEnum;
use App\Enum\RuntimeCacheEnum;
use App\Repository\SettingRepository;
use App\Service\System\Cache\RuntimeCacheService;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class SettingsService
{
    /**
     * @var SettingRepository
     */
    private $settingRepository;

    /**
     * @var RuntimeCacheService
     */
    private $runtimeCache;

    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(
        SettingRepository $settingRepository,
        RuntimeCacheService $runtimeCache,
        CacheInterface $cache
    ) {
        $this->settingRepository = $settingRepository;
        $this->cache = $cache;
        $this->runtimeCache = $runtimeCache;
    }

    public function getSettingsDto(): SettingsDto
    {
        return $this->runtimeCache->get(RuntimeCacheEnum::SETTINGS, function (): SettingsDto {
            return $this->cache->get(AppCacheEnum::SETTINGS, function (ItemInterface $item): SettingsDto {
                $item->expiresAfter(300);

                return $this->getSettingsDtoWithoutCache();
            });
        });
    }

    public function getSettingsDtoWithoutCache(): SettingsDto
    {
        $settingsDto = new SettingsDto();
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        $settingList = $this->getSettingsIndexedByName();
        foreach ($settingList as $setting) {
            if ($propertyAccessor->isWritable($settingsDto, $setting->getName())) {
                if ('' === $setting->getValue()) {
                    continue;
                }

                $propertyAccessor->setValue(
                    $settingsDto,
                    $setting->getName(),
                    $setting->getValue()
                );
            }
        }

        return $settingsDto;
    }

    /**
     * @return Setting[]
     */
    public function getSettingsIndexedByName(): array
    {
        $qb = $this->settingRepository->createQueryBuilder('setting');
        $qb->indexBy('setting', 'setting.name');

        return $qb->getQuery()->getResult();
    }
}
