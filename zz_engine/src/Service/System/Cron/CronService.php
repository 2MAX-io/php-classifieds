<?php

declare(strict_types=1);

namespace App\Service\System\Cron;

use App\Entity\System\SystemLog;
use App\Enum\AppLockEnumInterface;
use App\Helper\DateHelper;
use App\Service\Setting\SettingsDto;
use App\Service\System\Cron\Action\Base\CronActionInterface;
use App\Service\System\Cron\Action\Base\CronAtNightInterface;
use App\Service\System\Cron\Dto\CronDto;
use App\Service\System\SystemLog\SystemLogService;
use Symfony\Component\Lock\LockFactory;

class CronService
{
    /**
     * @var CronActionInterface[]|iterable
     */
    private $cronActionList;

    /**
     * @var SystemLogService
     */
    private $systemLogService;

    /**
     * @var SettingsDto
     */
    private $settingsDto;

    /**
     * @var LockFactory
     */
    private $lockFactory;

    /**
     * @param CronActionInterface[]|iterable $cronActionList
     */
    public function __construct(
        iterable $cronActionList,
        SystemLogService $systemLogService,
        SettingsDto $settingsDto,
        LockFactory $lockFactory
    ) {
        $this->cronActionList = $cronActionList;
        $this->systemLogService = $systemLogService;
        $this->lockFactory = $lockFactory;
        $this->settingsDto = $settingsDto;
    }

    public function run(CronDto $cronDto): void
    {
        $lock = $this->lockFactory->createLock(AppLockEnumInterface::CRON, 3600);
        if (!$lock->acquire()) {
            return;
        }

        try {
            foreach ($this->cronActionList as $cronAction) {
                $runOnlyAtNight = $cronAction instanceof CronAtNightInterface && !$this->isNightTime($cronDto);
                if ($runOnlyAtNight) {
                    continue;
                }

                $cronAction->runFromCron($cronDto);
            }

            $this->systemLogService->addSystemLog(SystemLog::CRON_RUN_TYPE, 'cron executed');
        } finally {
            $lock->release();
        }
    }

    private function isNightTime(CronDto $cronDto): bool
    {
        if ($cronDto->isNight()) {
            return true;
        }

        $hourOfDay = DateHelper::carbonNow()->setTimezone($this->settingsDto->getTimezone())->hour;

        return 1 <= $hourOfDay && $hourOfDay <= 4;
    }
}
