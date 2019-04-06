<?php

declare(strict_types=1);

namespace App\Service\System\Cron\Action\DeleteNotEnabledUsers;

use App\Helper\DateHelper;
use App\Service\System\Cron\Action\Base\CronActionInterface;
use App\Service\System\Cron\Action\Base\CronAtNightInterface;
use App\Service\System\Cron\Dto\CronDto;
use App\Service\System\RunEvery\RunEveryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class DeleteNotEnabledUsersCronService implements CronActionInterface, CronAtNightInterface, MessageHandlerInterface
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

    public function __invoke(DeleteNotEnabledUsersMessage $deleteNotEnabledUsersMessage): void
    {
        $lock = $this->lockFactory->createLock('lock_deleteNotEnabledUsers', 3600);
        if (!$lock->acquire()) {
            return;
        }

        try {
            $pdo = $this->em->getConnection();
            $pdo->executeQuery('
                DELETE user FROM user
                LEFT JOIN listing ON user.id = listing.user_id
                WHERE TRUE
                    && user.enabled = FALSE
                    && user.last_login IS NULL
                    && user.registration_date < :deleteOlderThan
                    && listing.id IS NULL
            ', [
                'deleteOlderThan' => DateHelper::carbonNow()->subDays(7)->format(DateHelper::MYSQL_FORMAT),
            ]);
        } finally {
            $lock->release();
        }
    }

    public function runFromCron(CronDto $cronDto): void
    {
        if (!$cronDto->getIgnoreDelay() && !$this->runEveryService->canRunAgain('cron_deleteNotEnabledUsers', 3 * 3600)) {
            return;
        }

        $this->messageBus->dispatch(new DeleteNotEnabledUsersMessage());
    }
}
