<?php

declare(strict_types=1);

namespace App\Service\System\Cron\Action;

use App\Enum\AppCacheEnum;
use App\Helper\DateHelper;
use App\Service\System\Cron\Action\Base\CronActionInterface;
use App\Service\System\Cron\Action\Base\CronAtNightInterface;
use App\Service\System\Cron\Dto\CronDto;
use App\Service\System\RunEvery\RunEveryService;
use Doctrine\ORM\EntityManagerInterface;

class DeactivateExpiredService implements CronActionInterface, CronAtNightInterface
{
    /**
     * @var RunEveryService
     */
    private $runEveryService;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(RunEveryService $runEveryService, EntityManagerInterface $em)
    {
        $this->runEveryService = $runEveryService;
        $this->em = $em;
    }

    public function runFromCron(CronDto $cronDto): void
    {
        if (!$cronDto->getIgnoreDelay() && !$this->runEveryService->canRunAgain(AppCacheEnum::CRON_DEACTIVATE_EXPIRED, 3600)) {
            return;
        }

        /** @var \PDO $pdo */
        $pdo = $this->em->getConnection();
        $query = $pdo->prepare('
            UPDATE listing 
            SET user_deactivated = 1 
            WHERE valid_until_date <= :olderThan
        ');
        $query->bindValue(':olderThan', DateHelper::carbonNow()->subDays(90));
        $query->execute();
    }
}
