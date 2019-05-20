<?php

declare(strict_types=1);

namespace App\Service\Setting;

use App\Entity\Setting;
use App\Repository\SettingRepository;
use App\System\Cache\RuntimeCacheInterface;
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

    public function save(SettingsDto $settingsDto)
    {
        $properties = $this->propertyInfoExtractor->getProperties($settingsDto);
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $settingList = $this->getSettingsIndexedByName();

        foreach ($properties as $property) {
            $setting = null;
            if (\array_key_exists($property, $settingList)) {
                /** @var Setting $setting */
                $setting = $settingList[$property];

                if ($setting->getName() === $property) {
                    $setting->setValue((string) $propertyAccessor->getValue($settingsDto, $property));
                }
            } else {
                $setting = new Setting();
                $setting->setName($property);
                $setting->setValue((string) $propertyAccessor->getValue($settingsDto, $property));
            }

            $setting->setLastUpdateDate(new \DateTime());
            $this->em->persist($setting);
        }

        $this->em->flush();
        $this->cache->delete(RuntimeCacheInterface::SETTINGS_CACHE);
        $this->arrayCache->delete(RuntimeCacheInterface::SETTINGS_CACHE);
    }

    public function getSettingsDto(): SettingsDto
    {
        if ($this->arrayCache->has(RuntimeCacheInterface::SETTINGS_CACHE)) {
            return $this->arrayCache->get(RuntimeCacheInterface::SETTINGS_CACHE);
        }

        if ($this->cache->has(RuntimeCacheInterface::SETTINGS_CACHE)) {
            return $this->cache->get(RuntimeCacheInterface::SETTINGS_CACHE);
        }

        $settingsDto = $this->getSettingsDtoWithoutCache();

        $this->arrayCache->set(RuntimeCacheInterface::SETTINGS_CACHE, $settingsDto);
        $this->cache->set(RuntimeCacheInterface::SETTINGS_CACHE, $settingsDto, 300);

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
