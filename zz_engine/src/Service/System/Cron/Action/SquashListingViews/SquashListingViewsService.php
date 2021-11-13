<?php

declare(strict_types=1);

namespace App\Service\System\Cron\Action\SquashListingViews;

use App\Enum\AppCacheEnum;
use App\Service\System\Cron\Action\Base\CronActionInterface;
use App\Service\System\Cron\Action\Base\CronAtNightInterface;
use App\Service\System\Cron\Dto\CronDto;
use App\Service\System\RunEvery\RunEveryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @see SquashListingViewsMessage
 */
class SquashListingViewsService implements CronActionInterface, CronAtNightInterface, MessageHandlerInterface
{
    /**
     * @var RunEveryService
     */
    private $runEveryService;

    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(
        RunEveryService $runEveryService,
        MessageBusInterface $messageBus,
        EntityManagerInterface $em
    ) {
        $this->runEveryService = $runEveryService;
        $this->em = $em;
        $this->messageBus = $messageBus;
    }

    public function __invoke(SquashListingViewsMessage $squashListingViewsMessage): void
    {
        $this->squashListingViews();
    }

    public function runFromCron(CronDto $cronDto): void
    {
        if (!$cronDto->getIgnoreDelay() && !$this->runEveryService->canRunAgain(AppCacheEnum::CRON_SQUASH_LISTING_VIEWS, 16 * 3600)) {
            return;
        }

        $this->messageBus->dispatch(new SquashListingViewsMessage());
    }

    public function squashListingViews(): void
    {
        /** @var \PDO $pdo */
        $pdo = $this->em->getConnection()->getWrappedConnection()->getWrappedConnection();

        $stmt = $pdo->query('SELECT id FROM listing_view ORDER BY id DESC');
        if (false === $stmt) {
            throw new \RuntimeException('could not execute query');
        }
        $newestListingViewId = (int) $stmt->fetchColumn();

        $stmt = $pdo->prepare('
            UPDATE listing_view JOIN (
                SELECT
                    listing_id, 
                    MIN(id) AS minListingViewId, 
                    SUM(view_count) AS sumViewCount, 
                    MAX(datetime) AS maxDatetime,
                    null
                FROM listing_view
                WHERE id <= :newestListingViewId
                GROUP BY listing_id
            ) viewCountSum
            ON 
                listing_view.id = viewCountSum.minListingViewId 
                && listing_view.listing_id = viewCountSum.listing_id
            SET
                view_count = sumViewCount,
                datetime = maxDatetime
            WHERE 1
        ');
        $stmt->bindValue(':newestListingViewId', $newestListingViewId);
        $stmt->execute();

        $stmt = $pdo->prepare('
            DELETE listing_view 
            FROM listing_view 
            JOIN (
                SELECT listing_id 
                FROM listing_view 
                GROUP BY listing_id 
                HAVING count(1) > 1
            ) listingWithMoreThanOneView ON listing_view.listing_id = listingWithMoreThanOneView.listing_id
            WHERE true
                && view_count = 1 
                && id <= :newestListingViewId
        ');
        $stmt->bindValue(':newestListingViewId', $newestListingViewId);
        $stmt->execute();
    }
}
