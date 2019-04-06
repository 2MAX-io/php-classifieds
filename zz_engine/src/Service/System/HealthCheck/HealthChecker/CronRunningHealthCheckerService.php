<?php

declare(strict_types=1);

namespace App\Service\System\HealthCheck\HealthChecker;

use App\Entity\System\SystemLog;
use App\Helper\DateHelper;
use App\Service\System\HealthCheck\Base\HealthCheckerInterface;
use App\Service\System\HealthCheck\HealthCheckResultDto;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CronRunningHealthCheckerService implements HealthCheckerInterface
{
    /**
     * @var TranslatorInterface
     */
    private $trans;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(TranslatorInterface $trans, EntityManagerInterface $em)
    {
        $this->trans = $trans;
        $this->em = $em;
    }

    public function checkHealth(): HealthCheckResultDto
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('systemLog');
        $qb->from(SystemLog::class, 'systemLog');
        $qb->addOrderBy('systemLog.date', Criteria::DESC);
        $qb->setMaxResults(1);
        /** @var SystemLog|null $lastCronRunSystemLog */
        $lastCronRunSystemLog = $qb->getQuery()->getOneOrNullResult();

        if (null === $lastCronRunSystemLog) {
            return new HealthCheckResultDto(
                true,
                $this->trans->trans('trans.Cron for this application is not set up, cron did not run single time')
            );
        }

        $dateAfterCronShouldRun = DateHelper::carbonNow()->subHours(24)->startOfDay();
        if ($lastCronRunSystemLog->getDate() < $dateAfterCronShouldRun) {
            $lastRunDate = $lastCronRunSystemLog->getDateNotNull()->format('Y-m-d, H:i');

            return new HealthCheckResultDto(true, $this->trans->trans('trans.Cron for this application is not set up, cron did not run after: %date%', [
                '%date%' => $lastRunDate,
            ]));
        }

        if ($this->countUnhandledQueueMessages() > 0) {
            return new HealthCheckResultDto(true, $this->trans->trans('trans.Cron executing message queue is not working'));
        }

        return new HealthCheckResultDto(false);
    }

    private function countUnhandledQueueMessages(): int
    {
        /** @var \PDO $pdo */
        $pdo = $this->em->getConnection();
        $stmt = $pdo->prepare(<<<'EOT'
            SELECT COUNT(1)
            FROM zzzz_messenger_messages
            WHERE true 
            && delivered_at IS NULL
            && queue_name != 'failed'
            && available_at < :before_datetime
EOT);
        $stmt->bindValue(':before_datetime', DateHelper::carbonNow()->subHours(6));
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }
}
