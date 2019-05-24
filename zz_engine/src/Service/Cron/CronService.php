<?php

declare(strict_types=1);

namespace App\Service\Cron;

use App\Entity\SystemLog;
use App\Service\System\SystemLog\SystemLogService;
use App\System\Lock\AppLockInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Lock\Factory;

class CronService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var SystemLogService
     */
    private $systemLogService;

    /**
     * @var Factory
     */
    private $lockFactory;

    public function __construct(EntityManagerInterface $em, SystemLogService $systemLogService, Factory $lockFactory)
    {
        $this->em = $em;
        $this->systemLogService = $systemLogService;
        $this->lockFactory = $lockFactory;
    }

    public function run(): void
    {
        $lock = $this->lockFactory->createLock(AppLockInterface::CRON, 3600);

        if (!$lock->acquire()) {
            return;
        }

        try {
            $this->updateFeatured();
            $this->setMainImage();

            $this->systemLogService->addSystemLog(SystemLog::CRON_RUN_TYPE, "cron executed");
        } finally {
            $lock->release();
        }
    }

    private function updateFeatured(): void
    {
        /** @var \PDO $pdo */
        $pdo = $this->em->getConnection();
        $query = $pdo->prepare(/** @lang MySQL */ '
UPDATE listing SET featured=0 WHERE featured_until_date <= :now OR featured_until_date IS NULL
');
        $query->bindValue(':now', date('Y-m-d 00:00:00'));
        $query->execute();
    }

    private function setMainImage(): void
    {
        /** @var \PDO $pdo */
        $pdo = $this->em->getConnection();
        $query = $pdo->prepare(/** @lang MySQL */ '
UPDATE listing JOIN (
    SELECT path, listing_id FROM listing_file GROUP BY listing_file.id ORDER BY sort ASC
) listing_file ON listing_file.listing_id = listing.id
SET listing.main_image = listing_file.path WHERE 1;
');
        $query->execute();
    }
}
