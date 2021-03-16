<?php

declare(strict_types=1);

namespace App\Service\System\Cron\Action\DeleteExpiredListingFiles;

use App\Enum\AppCacheEnum;
use App\Enum\AppLockEnumInterface;
use App\Service\Setting\SettingsService;
use App\Service\System\Cron\Action\Base\CronActionInterface;
use App\Service\System\Cron\Action\Base\CronAtNightInterface;
use App\Service\System\Cron\Dto\CronDto;
use App\Service\System\Maintenance\DeleteOldListingFiles\DeleteExpiredListingFilesService;
use App\Service\System\Maintenance\DeleteOldListingFiles\Dto\DeleteExpiredListingFilesDto;
use App\Service\System\RunEvery\RunEveryService;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class DeleteExpiredListingFilesCronService implements CronActionInterface, CronAtNightInterface, MessageHandlerInterface
{
    /**
     * @var DeleteExpiredListingFilesService
     */
    private $deleteExpiredListingFilesService;

    /**
     * @var SettingsService
     */
    private $settingsService;

    /**
     * @var LockFactory
     */
    private $lockFactory;

    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    /**
     * @var RunEveryService
     */
    private $runEveryService;

    public function __construct(
        DeleteExpiredListingFilesService $deleteExpiredListingFilesService,
        SettingsService $settingsService,
        RunEveryService $runEveryService,
        LockFactory $lockFactory,
        MessageBusInterface $messageBus
    ) {
        $this->deleteExpiredListingFilesService = $deleteExpiredListingFilesService;
        $this->settingsService = $settingsService;
        $this->lockFactory = $lockFactory;
        $this->messageBus = $messageBus;
        $this->runEveryService = $runEveryService;
    }

    public function __invoke(DeleteExpiredListingFilesMessage $deleteExpiredListingFilesMessage): void
    {
        $lock = $this->lockFactory->createLock(AppLockEnumInterface::DELETE_EXPIRED_LISTING_FILES, 3600);
        if (!$lock->acquire()) {
            return;
        }

        try {
            $settingsDto = $this->settingsService->getSettingsDto();
            if (!$settingsDto->getDeleteExpiredListingFilesEnabled()) {
                return;
            }
            $daysOldToDelete = $settingsDto->getDeleteExpiredListingFilesOlderThanDays();
            $deleteOldListingFilesDto = new DeleteExpiredListingFilesDto();
            $deleteOldListingFilesDto->setDaysOldToDelete($daysOldToDelete);
            $deleteOldListingFilesDto->setPerformFileDeletion(true);
            $this->deleteExpiredListingFilesService->deleteExpiredListingFiles($deleteOldListingFilesDto);
        } finally {
            $lock->release();
        }
    }

    public function runFromCron(CronDto $cronDto): void
    {
        if (!$cronDto->getIgnoreDelay() && !$this->runEveryService->canRunAgain(AppCacheEnum::CRON_DELETE_EXPIRED_LISTING_FILES, 3 * 3600)) {
            return;
        }

        $this->messageBus->dispatch(new DeleteExpiredListingFilesMessage());
    }
}
