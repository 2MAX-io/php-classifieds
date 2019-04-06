<?php

declare(strict_types=1);

namespace App\Service\System\Cron\Action;

use App\Enum\AppCacheEnum;
use App\Helper\DateHelper;
use App\Service\System\Cron\Action\Base\CronActionInterface;
use App\Service\System\Cron\Dto\CronDto;
use App\Service\System\RunEvery\RunEveryService;
use Doctrine\ORM\EntityManagerInterface;

class RemoveFeaturedWhenExpiredService implements CronActionInterface
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
        if (!$cronDto->getIgnoreDelay() && !$this->runEveryService->canRunAgain(AppCacheEnum::CRON_EXPIRE_FEATURED, 3600)) {
            return;
        }

        /** @var \PDO $pdo */
        $pdo = $this->em->getConnection();
        $query = $pdo->prepare('
            UPDATE listing 
            SET 
                featured = 0,
                featured_priority = 0
            WHERE true
                && featured = 1 
                && (featured_until_date <= :now OR featured_until_date IS NULL)
        ');
        $query->bindValue(':now', DateHelper::date(DateHelper::MYSQL_FORMAT));
        $query->execute();
    }
}
