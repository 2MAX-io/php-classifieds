<?php

declare(strict_types=1);

namespace App\Service\Setting;

use App\Entity\Setting;
use App\Helper\Str;
use App\Repository\SettingRepository;
use App\Service\System\Cache\RuntimeCacheService;
use App\System\Cache\AppCacheEnum;
use App\System\Cache\RuntimeCacheEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

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
    private $cache;

    /**
     * @var RuntimeCacheService
     */
    private $runtimeCache;

    public function __construct(
        PropertyInfoExtractorInterface $propertyInfoExtractor,
        SettingRepository $settingRepository,
        EntityManagerInterface $em,
        RuntimeCacheService $runtimeCache,
        CacheInterface $cache
    ) {
        $this->propertyInfoExtractor = $propertyInfoExtractor;
        $this->settingRepository = $settingRepository;
        $this->em = $em;
        $this->cache = $cache;
        $this->runtimeCache = $runtimeCache;
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
        $this->cache->delete(AppCacheEnum::SETTINGS);
        $this->runtimeCache->delete(RuntimeCacheEnum::SETTINGS);
    }

    public function getSettingsDto(): SettingsDto
    {
        return $this->runtimeCache->get(RuntimeCacheEnum::SETTINGS, function(): SettingsDto {
            return $this->cache->get(AppCacheEnum::SETTINGS, function(ItemInterface $item): SettingsDto {
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

    public function getAllowedCharactersEnabled(): bool
    {
        return $this->getSettingsDto()->getAllowedCharactersEnabled();
    }

    public function getAllowedCharacters(): string
    {
        return $this->getSettingsDto()->getAllowedCharacters();
    }

    public function getPaymentGateway(): string
    {
        return $this->getSettingsDto()->getPaymentGateway();
    }

    public function getPaymentPrzelewy24MerchantId(): string
    {
        return $this->getSettingsDto()->getPaymentPrzelewy24MerchantId();
    }

    public function getPaymentPrzelewy24PosId(): string
    {
        return $this->getSettingsDto()->getPaymentPrzelewy24PosId();
    }

    public function getPaymentPrzelewy24Crc(): string
    {
        return $this->getSettingsDto()->getPaymentPrzelewy24Crc();
    }

    public function getPaymentPrzelewy24Mode(): string
    {
        return $this->getSettingsDto()->getPaymentPrzelewy24Mode();
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
