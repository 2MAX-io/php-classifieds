<?php

declare(strict_types=1);

namespace App\Service\System\HealthCheck\HealthChecker\Settings;

use App\Entity\Setting;
use App\Form\Admin\SettingsType;
use App\Helper\Arr;
use App\Helper\ExceptionHelper;
use App\Helper\Helper;
use App\Helper\Str;
use App\Service\Setting\SettingsService;
use App\Service\System\HealthCheck\Base\HealthCheckerInterface;
use App\Service\System\HealthCheck\HealthCheckResultDto;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
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

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        EntityManagerInterface $em,
        SettingsService $settingsService,
        TranslatorInterface $trans,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->trans = $trans;
        $this->settingsService = $settingsService;
        $this->logger = $logger;
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

            $settingName = Helper::getPropertyNameFromMethodName($method);
            if (\in_array(
                $settingName,
                $this->getExcludedSettings(),
                true
            )) {
                continue;
            }

            $value = $settingsDto->$method();
            if (Str::emptyTrim($value)) {
                $failed = true;
                $missingSettings[] = $settingName;
                $this->logger->debug('method returns empty', [$method]);
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

        $missingSettings = \array_unique($missingSettings);
        $missingSettings = \array_map(
            function (string $settingName): string {
                return $this->getSettingLabel($settingName);
            },
            $missingSettings
        );
        if ($failed) {
            return new HealthCheckResultDto(true, $this->trans->trans('trans.Set all application settings. Missing settings: %missing%', [
                '%missing%' => \implode(', ', $missingSettings)
            ]));
        }

        return new HealthCheckResultDto(false);
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
            'facebookSignInAppId',
            'facebookSignInAppSecret',
            'googleSignInClientId',
            'googleSignInClientSecret',
        ];
    }

    private function getSettingLabel(string $settingName): string
    {
        try {
            $settingType = new SettingsType();
            $formBuilderMock = new MockFormBuilder();
            $settingType->buildForm($formBuilderMock, []);
        } catch (\Throwable $e) {
            $this->logger->critical('getting label from settings form type failed', ExceptionHelper::flatten($e));

            return $settingName;
        }

        if (!isset($formBuilderMock->formChildrenList[$settingName]['label'])) {
            return $settingName;
        }
        $label = $formBuilderMock->formChildrenList[$settingName]['label'];

        return $this->trans->trans($label);
    }
}
