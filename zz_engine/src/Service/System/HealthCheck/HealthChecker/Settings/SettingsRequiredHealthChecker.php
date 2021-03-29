<?php

declare(strict_types=1);

namespace App\Service\System\HealthCheck\HealthChecker\Settings;

use App\Form\Admin\Settings\Base\SettingTypeInterface;
use App\Helper\ArrayHelper;
use App\Helper\ClassHelper;
use App\Helper\ExceptionHelper;
use App\Helper\StringHelper;
use App\Repository\SettingRepository;
use App\Service\Setting\SettingsService;
use App\Service\System\HealthCheck\Base\HealthCheckerInterface;
use App\Service\System\HealthCheck\HealthChecker\Settings\Mock\MockFormBuilder;
use App\Service\System\HealthCheck\HealthCheckResultDto;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SettingsRequiredHealthChecker implements HealthCheckerInterface
{
    /**
     * @var SettingsService
     */
    private $settingsService;

    /**
     * @var TranslatorInterface
     */
    private $trans;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SettingRepository
     */
    private $settingRepository;

    /**
     * @var iterable|SettingTypeInterface[]
     */
    private $settingTypes;

    /**
     * @param iterable|SettingTypeInterface[] $settingTypes
     */
    public function __construct(
        iterable $settingTypes,
        SettingsService $settingsService,
        SettingRepository $settingRepository,
        TranslatorInterface $trans,
        LoggerInterface $logger
    ) {
        $this->trans = $trans;
        $this->settingsService = $settingsService;
        $this->logger = $logger;
        $this->settingRepository = $settingRepository;
        $this->settingTypes = $settingTypes;
    }

    public function checkHealth(): HealthCheckResultDto
    {
        $settingsDto = $this->settingsService->getSettingsDtoWithoutCache();
        $failed = false;
        $missingSettings = [];
        foreach (\get_class_methods($settingsDto) as $method) {
            if (ArrayHelper::inArray($method, ['__construct'])) {
                continue;
            }

            if (StringHelper::beginsWith($method, 'set')) {
                continue;
            }

            $settingName = ClassHelper::getPropertyNameFromMethodName($method);
            $settingValue = $settingsDto->{$method}();
            if ($this->shouldSkip($settingName, $settingValue)) {
                continue;
            }
            if (StringHelper::emptyTrim($settingValue)) {
                $failed = true;
                $missingSettings[] = $settingName;
                $this->logger->debug('method returns empty', [$method]);
            }
        }

        foreach ($this->settingRepository->getAllSettings() as $setting) {
            $settingName = $setting->getName();
            if ($this->shouldSkip($settingName, $setting->getValue())) {
                continue;
            }

            if (StringHelper::emptyTrim($setting->getValue())) {
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
            return new HealthCheckResultDto(
                true,
                $this->trans->trans(
                    'trans.Set all application settings. Missing settings: %missing%',
                    [
                        '%missing%' => \implode(', ', $missingSettings),
                    ]
                )
            );
        }

        return new HealthCheckResultDto(false);
    }

    /**
     * @param mixed $settingValue
     */
    private function shouldSkip(string $settingName, $settingValue): bool
    {
        if (\in_array($settingName, $this->getExcludedSettings(), true)) {
            return true;
        }
        if ('thousandSeparator' === $settingName && ' ' === $settingValue) {
            return true;
        }

        return false;
    }

    /**
     * @return string[]
     */
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
            'invoiceCompanyName',
            'invoiceTaxNumber',
            'invoiceCity',
            'invoiceStreet',
            'invoiceBuildingNumber',
            'invoiceUnitNumber',
            'invoiceZipCode',
            'invoiceCountry',
            'invoiceEmail',
            'paymentPrzelewy24MerchantId',
            'paymentPrzelewy24PosId',
            'paymentPrzelewy24Crc',
            'invoiceSoldItemDescription',
            'invoiceNumberPrefix',
            'defaultAdvertisementZoneId',
            'paymentPayPalClientId',
            'paymentPayPalClientSecret',
        ];
    }

    private function getSettingLabel(string $settingName): string
    {
        try {
            $formBuilderMock = new MockFormBuilder();
            foreach ($this->settingTypes as $settingType) {
                $settingType->buildForm($formBuilderMock, []);
            }
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
