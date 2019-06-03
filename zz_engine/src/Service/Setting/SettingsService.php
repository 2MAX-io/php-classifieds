<?php

declare(strict_types=1);

namespace App\Service\Setting;

use App\Entity\Setting;
use App\Helper\Str;
use App\Repository\SettingRepository;
use App\System\Cache\RuntimeCacheEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Simple\ArrayCache;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Psr\SimpleCache\CacheInterface;

class SettingsService
{
    /**
     * @var PropertyInfoExtractorInterface
     */
    private $propertyInfoExtractor;

    /**
     * @var SettingRepository
     */
    private $settingRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var CacheInterface
     */
    private $arrayCache;

    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(
        PropertyInfoExtractorInterface $propertyInfoExtractor,
        SettingRepository $settingRepository,
        EntityManagerInterface $em,
        ArrayCache $arrayCache,
        CacheInterface $cache
    ) {
        $this->propertyInfoExtractor = $propertyInfoExtractor;
        $this->settingRepository = $settingRepository;
        $this->em = $em;
        $this->arrayCache = $arrayCache;
        $this->cache = $cache;
    }

    public function save(SettingsDto $settingsDto): void
    {
        $properties = $this->propertyInfoExtractor->getProperties($settingsDto);
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $settingList = $this->getSettingsIndexedByName();
        $currentDate = new \DateTime();

        foreach ($properties as $property) {
            $setting = null;
            if (\array_key_exists($property, $settingList)) {
                /** @var Setting $setting */
                $setting = $settingList[$property];

                if ($setting->getName() === $property) {
                    $setting->setValue(Str::toString($propertyAccessor->getValue($settingsDto, $property)));
                }
            } else {
                $setting = new Setting();
                $setting->setName($property);
                $setting->setValue(Str::toString($propertyAccessor->getValue($settingsDto, $property)));
            }
            $setting->setLastUpdateDate($currentDate);
            $this->em->persist($setting);
        }

        $this->em->flush();
        $this->cache->delete(RuntimeCacheEnum::SETTINGS);
        $this->arrayCache->delete(RuntimeCacheEnum::SETTINGS);
    }

    public function getSettingsDto(): SettingsDto
    {
        if ($this->arrayCache->has(RuntimeCacheEnum::SETTINGS)) {
            return $this->arrayCache->get(RuntimeCacheEnum::SETTINGS);
        }

        if ($this->cache->has(RuntimeCacheEnum::SETTINGS)) {
            return $this->cache->get(RuntimeCacheEnum::SETTINGS);
        }

        $settingsDto = $this->getSettingsDtoWithoutCache();

        $this->arrayCache->set(RuntimeCacheEnum::SETTINGS, $settingsDto);
        $this->cache->set(RuntimeCacheEnum::SETTINGS, $settingsDto, 300);

        return $settingsDto;
    }

    public function getSettingsDtoWithoutCache(): SettingsDto
    {
        $settingsDto = new SettingsDto();
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        $settingList = $this->getSettingsIndexedByName();
        foreach ($settingList as $setting) {
            if ($propertyAccessor->isWritable($settingsDto, $setting->getName())) {
                $propertyAccessor->setValue(
                    $settingsDto,
                    $setting->getName(),
                    $setting->getValue()
                );
            }
        }

        return $settingsDto;
    }

    public function getLanguageTwoLetters(): string
    {
        return $this->getSettingsDto()->getLanguageTwoLetters();
    }

    public function getCurrency(): string
    {
        return $this->getSettingsDto()->getCurrency();
    }

    public function getAllowedCharacters(): string
    {
        return $this->getSettingsDto()->getAllowedCharacters();
    }

    /**
     * @return Setting[]
     */
    private function getSettingsIndexedByName(): array
    {
        $qb = $this->settingRepository->createQueryBuilder('setting');
        $qb->indexBy('setting', 'setting.name');

        return $qb->getQuery()->getResult();
    }
}
