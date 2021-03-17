<?php

declare(strict_types=1);

namespace App\Service\System\Cron\Action\CleanDatabase;

use App\Entity\System\SystemLog;
use App\Service\System\Cron\Action\Base\CronActionInterface;
use App\Service\System\Cron\Action\Base\CronAtNightInterface;
use App\Service\System\Cron\Dto\CronDto;
use App\Service\System\RunEvery\RunEveryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class CleanDatabaseCronService implements CronActionInterface, CronAtNightInterface, MessageHandlerInterface
{
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

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(
        RunEveryService $runEveryService,
        LockFactory $lockFactory,
        MessageBusInterface $messageBus,
        EntityManagerInterface $em
    ) {
        $this->lockFactory = $lockFactory;
        $this->messageBus = $messageBus;
        $this->runEveryService = $runEveryService;
        $this->em = $em;
    }

    public function __invoke(CleanDatabaseMessage $deleteExpiredListingFilesMessage): void
    {
        $lockName = 'lock_cleanDatabase';
        $lock = $this->lockFactory->createLock($lockName, 3600);
        if (!$lock->acquire()) {
            return;
        }

        try {
            $this->cleanCronRunLogs();
        } finally {
            $lock->release();
        }
    }

    public function runFromCron(CronDto $cronDto): void
    {
        if (!$cronDto->getIgnoreDelay() && !$this->runEveryService->canRunAgain('cron_cleanDatabase', 16 * 3600)) {
            return;
        }

        $this->messageBus->dispatch(new CleanDatabaseMessage());
    }

    private function cleanCronRunLogs(): void
    {
        /** @var \PDO $pdo */
        $pdo = $this->em->getConnection();
        $stmt = $pdo->prepare('
            DELETE FROM zzzz_system_log
            WHERE true
                && type = :cronRunType
                && id < (
                    SELECT lastCronId FROM (
                        SELECT MAX(id) AS lastCronId
                        FROM zzzz_system_log
                        WHERE true
                            && type = :cronRunType
                    ) AS lastCronId
                )
        ');
        $stmt->bindValue(':cronRunType', SystemLog::CRON_RUN_TYPE);
        $stmt->execute();
    }
}
