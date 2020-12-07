<?php

declare(strict_types=1);

namespace App\Service\Cron;

use App\Entity\SystemLog;
use App\Service\Cron\Dto\CronMainDto;
use App\Service\System\RunEvery\RunEveryService;
use App\Service\System\SystemLog\SystemLogService;
use App\System\Cache\AppCacheEnum;
use App\System\Lock\AppLockInterface;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Symfony\Component\Lock\LockFactory;
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
     * @var LockFactory
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

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator,
        SystemLogService $systemLogService,
        RunEveryService $runEveryService,
        LockFactory $lockFactory,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->systemLogService = $systemLogService;
        $this->lockFactory = $lockFactory;
        $this->urlGenerator = $urlGenerator;
        $this->runEveryService = $runEveryService;
        $this->logger = $logger;
    }

    public function run(CronMainDto $cronMainDto): void
    {
        $lock = $this->lockFactory->createLock(AppLockInterface::CRON, 3600);

        if (!$lock->acquire()) {
            return;
        }

        try {
            $this->removeFeaturedWhenExpired($cronMainDto);
            $this->deactivateExpired($cronMainDto);
            $this->setMainImage($cronMainDto);
            $this->openIndexPage($cronMainDto);
            $this->squashListingViews($cronMainDto);

            $this->systemLogService->addSystemLog(SystemLog::CRON_RUN_TYPE, 'cron executed');
        } finally {
            $lock->release();
        }
    }

    private function removeFeaturedWhenExpired(CronMainDto $cronMainDto): void
    {
        if (!$cronMainDto->getIgnoreDelay() && !$this->runEveryService->canRunAgain(AppCacheEnum::CRON_EXPIRE_FEATURED, 60 * 5)) {
            return;
        }

        /** @var \PDO $pdo */
        $pdo = $this->em->getConnection();
        $query = $pdo->prepare(<<<'TAG'
UPDATE listing SET featured=0 WHERE featured_until_date <= :now OR featured_until_date IS NULL
TAG
        );
        $query->bindValue(':now', \date('Y-m-d H:i:s'));
        $query->execute();
    }

    private function deactivateExpired(CronMainDto $cronMainDto): void
    {
        if (!$cronMainDto->getIgnoreDelay() && !$this->runEveryService->canRunAgain(AppCacheEnum::CRON_DEACTIVATE_EXPIRED, 60 * 23)) {
            return;
        }

        /** @var \PDO $pdo */
        $pdo = $this->em->getConnection();
        $query = $pdo->prepare(<<<'TAG'
UPDATE listing SET user_deactivated=1 WHERE valid_until_date <= :olderThan
TAG
        );
        $query->bindValue(':olderThan', Carbon::now()->subDays(90));
        $query->execute();
    }

    private function setMainImage(CronMainDto $cronMainDto): void
    {
        if (!$cronMainDto->getIgnoreDelay() && !$this->runEveryService->canRunAgain(AppCacheEnum::CRON_SET_MAIN_IMAGE, 60 * 25)) {
            return;
        }

        /** @var \PDO $pdo */
        $pdo = $this->em->getConnection();
        $pdo->exec(<<<'TAG'
UPDATE listing
    LEFT JOIN listing_file
    ON listing.id = listing_file.listing_id
        && listing_file.user_removed = 0
    LEFT JOIN (
        SELECT
            listing_id
          , MIN(sort) minSort
          , path
        FROM listing_file
        WHERE listing_file.user_removed = 0
        GROUP BY listing_id
    ) listingWithMinSort
    ON
        listing_file.listing_id = listingWithMinSort.listing_id
        && listing_file.sort = listingWithMinSort.minSort
SET listing.main_image = listing_file.path
WHERE 1
TAG
        );

        $this->logger->debug('set main image executed');
    }

    private function squashListingViews(CronMainDto $cronMainDto): void
    {
        if (!$cronMainDto->getIgnoreDelay() && !$this->runEveryService->canRunAgain(AppCacheEnum::CRON_SQUASH_LISTING_VIEWS, 60 * 60 * 6)) {
            return;
        }

        /** @var \PDO $pdo */
        $pdo = $this->em->getConnection();

        $stmt = $pdo->query('SELECT id FROM listing_view ORDER BY id DESC');
        $maxListingViewId = (int) $stmt->fetchColumn();

        $stmt = $pdo->prepare(<<<'TAG'
UPDATE listing_view JOIN (
    SELECT
        MIN(id) AS minListingViewId
        , listing_id
        , SUM(view_count) AS sumViewCount
        , MAX(datetime) AS maxDatetime
    FROM listing_view
    WHERE id <= :maxListingViewId
    GROUP BY listing_id
) viewCountSum
ON 
    listing_view.id = viewCountSum.minListingViewId 
    && listing_view.listing_id = viewCountSum.listing_id
SET
    view_count = sumViewCount
    , datetime = maxDatetime
WHERE 1
TAG);
        $stmt->bindValue(':maxListingViewId', $maxListingViewId);
        $stmt->execute();

        $stmt = $pdo->prepare(<<<'TAG'
DELETE listing_view FROM listing_view JOIN (
    SELECT listing_id FROM listing_view GROUP BY listing_id HAVING count(1) > 1
) listingWithMoreThanOneView ON listing_view.listing_id = listingWithMoreThanOneView.listing_id
WHERE 
    view_count = 1 
    && id <= :maxListingViewId
TAG);
        $stmt->bindValue(':maxListingViewId', $maxListingViewId);
        $stmt->execute();
    }

    private function openIndexPage(CronMainDto $cronMainDto): void
    {
        if (!$cronMainDto->getIgnoreDelay() && !$this->runEveryService->canRunAgain(AppCacheEnum::CRON_OPEN_INDEX, 40)) {
            return;
        }

        $client = new Client(
            [
                RequestOptions::TIMEOUT => 30,
                RequestOptions::CONNECT_TIMEOUT => 30,
                RequestOptions::READ_TIMEOUT => 30,
                RequestOptions::VERIFY => false,
            ]
        );

        $client->get($this->urlGenerator->generate('app_index', [], UrlGeneratorInterface::ABSOLUTE_URL));
    }
}
