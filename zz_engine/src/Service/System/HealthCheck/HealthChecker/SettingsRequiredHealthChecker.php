<?php

declare(strict_types=1);

namespace App\Service\System\HealthCheck\HealthChecker;

use App\Entity\Setting;
use App\Helper\Arr;
use App\Helper\Str;
use App\Service\Setting\SettingsService;
use App\Service\System\HealthCheck\Base\HealthCheckerInterface;
use App\Service\System\HealthCheck\HealthCheckResultDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SettingsRequiredHealthChecker implements HealthCheckerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TranslatorInterface
     */
    private $trans;

    /**
     * @var SettingsService
     */
    private $settingsService;

    public function __construct(EntityManagerInterface $em, SettingsService $settingsService, TranslatorInterface $trans)
    {
        $this->em = $em;
        $this->trans = $trans;
        $this->settingsService = $settingsService;
    }

    public function checkHealth(): HealthCheckResultDto
    {
        $settingsDto = $this->settingsService->getSettingsDtoWithoutCache();
        $failed = false;
        $missingSettings = [];
        foreach (\get_class_methods($settingsDto) as $method) {
            if (Arr::inArray($method, ['__construct'])) {
                continue;
            }

            if (Str::beginsWith($method,'set')) {
                continue;
            }

            if (\in_array($method, $this->getExcludedMethods(), true)) {
                continue;
            }

            $value = $settingsDto->$method();
            if (Str::emptyTrim($value)) {
                $failed = true;
                $missingSettings[] = $method;
            }
        }

        $qb = $this->em->getRepository(Setting::class)->createQueryBuilder('setting');
        /** @var Setting[] $settingList */
        $settingList = $qb->getQuery()->getResult();

        foreach ($settingList as $setting) {
            $settingName = $setting->getName();
            if (\in_array($settingName, $this->getExcludedSettings(), true)) {
                continue;
            }

            if (Str::emptyTrim($setting->getValue())) {
                $failed = true;
                $missingSettings[] = $settingName;
            }
        }

        if ($failed) {
            return new HealthCheckResultDto(true, $this->trans->trans('trans.Set all application settings. Missing settings: %missing%', [
                '%missing%' => \implode(', ', $missingSettings)
            ]));
        }

        return new HealthCheckResultDto(false);
    }

    private function getExcludedMethods(): array
    {
        return [
            'getLinkTermsConditions',
            'getAllowedCharacters',
            'getMasterSiteUrl',
            'getMasterSiteAnchorText',
            'isMasterSiteLinkShow',
            'getLinkPrivacyPolicy',
            'getLinkRejectionReason',
            'getLogoPath',
            'getLinkContact',
            'getLinkAdvertisement',
            'getCustomJavascriptInHead',
            'getCustomJavascriptBottom',
            'getCustomCss',
        ];
    }

    private function getExcludedSettings(): array
    {
        return [
            'linkTermsConditions',
            'allowedCharacters',
            'masterSiteUrl',
            'masterSiteAnchorText',
            'masterSiteLinkShow',
            'linkPrivacyPolicy',
            'linkRejectionReason',
            'logoPath',
            'linkContact',
            'linkAdvertisement',
            'customJavascriptBottom',
            'customJavascriptInHead',
            'customCss',
        ];
    }
}
