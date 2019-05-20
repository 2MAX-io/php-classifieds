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
        $settingsDto = $this->settingsService->getSettingsDto();
        $failed = false;
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
            if (Str::emptyTrim((string) $value)) {
                $failed = true;
            }
        }

        $qb = $this->em->getRepository(Setting::class)->createQueryBuilder('setting');
        /** @var Setting[] $settingList */
        $settingList = $qb->getQuery()->getResult();

        foreach ($settingList as $setting) {
            if (Str::emptyTrim($setting->getValue())) {
                $failed = true;
            }
        }

        if ($failed) {
            return new HealthCheckResultDto(true, $this->trans->trans('trans.Set all application settings'));
        }

        return new HealthCheckResultDto(false);
    }

    private function getExcludedMethods(): array
    {
        return ['getEmailConfigUrl'];
    }
}
