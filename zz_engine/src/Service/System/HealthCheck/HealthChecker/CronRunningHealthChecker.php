<?php

declare(strict_types=1);

namespace App\Service\System\HealthCheck\HealthChecker;

use App\Entity\SystemLog;
use App\Service\System\HealthCheck\Base\HealthCheckerInterface;
use App\Service\System\HealthCheck\HealthCheckResultDto;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CronRunningHealthChecker implements HealthCheckerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TranslatorInterface
     */
    private $trans;

    public function __construct(EntityManagerInterface $em, TranslatorInterface $trans)
    {
        $this->em = $em;
        $this->trans = $trans;
    }

    public function checkHealth(): HealthCheckResultDto
    {
        $qb = $this->em->getRepository(SystemLog::class)->createQueryBuilder('systemLog');
        $qb->addOrderBy('systemLog.date', 'DESC');
        $qb->setMaxResults(1);
        /** @var SystemLog $lastCronRunSystemLog */
        $lastCronRunSystemLog = $qb->getQuery()->getOneOrNullResult();

        if ($lastCronRunSystemLog === null) {
            return new HealthCheckResultDto(true, $this->trans->trans('trans.Cron for this application is not set up, cron did not run single time'));
        }

        $dateAfterCronShouldRun = Carbon::now()->subHours(24)->startOfDay();
        if ($lastCronRunSystemLog->getDate() < $dateAfterCronShouldRun) {
            $lastRunDate = $lastCronRunSystemLog->getDateNotNull()->format('Y-m-d, H:i');
            return new HealthCheckResultDto(true, $this->trans->trans('trans.Cron for this application is not set up, cron did not run after: %date%', [
                '%date%' => $lastRunDate,
            ]));
        }

        return new HealthCheckResultDto(false);
    }
}
