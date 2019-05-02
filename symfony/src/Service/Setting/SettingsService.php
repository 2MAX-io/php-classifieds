<?php

declare(strict_types=1);

namespace App\Service\Setting;

use App\Entity\Setting;
use App\Repository\SettingRepository;
use App\System\Cache\CacheService;
use App\System\Cache\LocalCacheInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;

class SettingsService implements LocalCacheInterface
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
    private $cache;

    public function __construct(
        PropertyInfoExtractorInterface $propertyInfoExtractor,
        SettingRepository $settingRepository,
        EntityManagerInterface $em,
        CacheInterface $cache
    ) {
        $this->propertyInfoExtractor = $propertyInfoExtractor;
        $this->settingRepository = $settingRepository;
        $this->em = $em;
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
                    $setting->setValue($propertyAccessor->getValue($settingsDto, $property));
                }
            } else {
                $setting = new Setting();
                $setting->setName($property);
                $setting->setValue($propertyAccessor->getValue($settingsDto, $property));
            }

            $setting->setLastUpdateDate(new \DateTime());
            $this->em->persist($setting);
        }

        $this->em->flush();
        $this->cache->delete(CacheService::TWIG_SETTINGS_CACHE);
    }

    public function getHydratedSettingsDto(): SettingsDto
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
        return $this->getHydratedSettingsDto()->getLanguageTwoLetters();
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
