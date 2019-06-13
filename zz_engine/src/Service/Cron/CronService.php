<?php

declare(strict_types=1);

namespace App\Service\Cron;

use App\Entity\SystemLog;
use App\Service\System\RunEvery\RunEveryService;
use App\Service\System\SystemLog\SystemLogService;
use App\System\Cache\AppCacheEnum;
use App\System\Lock\AppLockInterface;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var RunEveryService
     */
    private $runEveryService;

    public function __construct(
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator,
        SystemLogService $systemLogService,
        RunEveryService $runEveryService,
        Factory $lockFactory
    ) {
        $this->em = $em;
        $this->systemLogService = $systemLogService;
        $this->lockFactory = $lockFactory;
        $this->urlGenerator = $urlGenerator;
        $this->runEveryService = $runEveryService;
    }

    public function run(): void
    {
        $lock = $this->lockFactory->createLock(AppLockInterface::CRON, 3600);

        if (!$lock->acquire()) {
            return;
        }

        try {
            $this->removeFeaturedWhenExpired();
            $this->deactivateExpired();
            $this->setMainImage();
            $this->openIndexPage();

            $this->systemLogService->addSystemLog(SystemLog::CRON_RUN_TYPE, 'cron executed');
        } finally {
            $lock->release();
        }
    }

    private function removeFeaturedWhenExpired(): void
    {
        if (!$this->runEveryService->canRunAgain(AppCacheEnum::CRON_EXPIRE_FEATURED, 60*20)) {
            return;
        }

        /** @var \PDO $pdo */
        $pdo = $this->em->getConnection();
        $query = $pdo->prepare(
        /** @lang MySQL */ '
UPDATE listing SET featured=0 WHERE featured_until_date <= :now OR featured_until_date IS NULL
'
        );
        $query->bindValue(':now', date('Y-m-d H:i:s'));
        $query->execute();
    }

    private function deactivateExpired(): void
    {
        if (!$this->runEveryService->canRunAgain(AppCacheEnum::CRON_DEACTIVATE_EXPIRED, 60*23)) {
            return;
        }

        /** @var \PDO $pdo */
        $pdo = $this->em->getConnection();
        $query = $pdo->prepare(
        /** @lang MySQL */ '
UPDATE listing SET user_deactivated=1 WHERE valid_until_date <= :olderThan
'
        );
        $query->bindValue(':olderThan', Carbon::now()->subDays(90));
        $query->execute();
    }

    private function setMainImage(): void
    {
        if (!$this->runEveryService->canRunAgain(AppCacheEnum::CRON_SET_MAIN_IMAGE, 60*25)) {
            return;
        }

        /** @var \PDO $pdo */
        $pdo = $this->em->getConnection();
        $query = $pdo->prepare(
        /** @lang MySQL */ '
UPDATE listing
    JOIN listing_file
    ON listing.id = listing_file.listing_id
    JOIN (
        SELECT listing_id, MIN(sort) minSort, path FROM listing_file GROUP BY listing_id
    ) minSortJoin
    ON minSortJoin.listing_id = listing_file.listing_id && listing_file.sort = minSortJoin.minSort
SET listing.main_image = listing_file.path
WHERE 1
'
        );
        $query->execute();
    }

    private function openIndexPage(): void
    {
        if (!$this->runEveryService->canRunAgain(AppCacheEnum::CRON_OPEN_INDEX, 40)) {
            return;
        }

        $client = new Client([
            RequestOptions::TIMEOUT => 30,
            RequestOptions::CONNECT_TIMEOUT => 30,
            RequestOptions::READ_TIMEOUT => 30,
            RequestOptions::VERIFY => false,
        ]);

        $client->get($this->urlGenerator->generate('app_index', [], UrlGeneratorInterface::ABSOLUTE_URL));
    }
}
