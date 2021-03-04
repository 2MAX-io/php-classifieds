<?php

declare(strict_types=1);

namespace App\Service\System\Cron\Action\UpdateMainImage;

use App\Enum\AppCacheEnum;
use App\Service\System\Cron\Action\Base\CronActionInterface;
use App\Service\System\Cron\Action\Base\CronAtNightInterface;
use App\Service\System\Cron\Dto\CronDto;
use App\Service\System\RunEvery\RunEveryService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class UpdateMainImageService implements CronActionInterface, CronAtNightInterface, MessageHandlerInterface
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(
        RunEveryService $runEveryService,
        MessageBusInterface $messageBus,
        EntityManagerInterface $em,
        LoggerInterface $logger
    ) {
        $this->runEveryService = $runEveryService;
        $this->em = $em;
        $this->logger = $logger;
        $this->messageBus = $messageBus;
    }

    public function __invoke(UpdateMainImageMessage $updateMainImageMessage): void
    {
        $this->updateMainImage();
    }

    public function runFromCron(CronDto $cronDto): void
    {
        if (!$cronDto->getIgnoreDelay() && !$this->runEveryService->canRunAgain(AppCacheEnum::CRON_SET_MAIN_IMAGE, 16 * 3600)) {
            return;
        }

        $this->messageBus->dispatch(new UpdateMainImageMessage());
    }

    public function updateMainImage(): void
    {
        /** @var \PDO $pdo */
        $pdo = $this->em->getConnection();
        $pdo->exec('
            UPDATE listing
            LEFT JOIN (
                SELECT
                    listing_file.listing_id AS listing_id,
                    listing_file.path AS main_image,
                    null
                FROM listing_file
                JOIN (
                    SELECT
                        listing_id,
                        MIN(sort) minSort,
                        path,
                        null
                    FROM listing_file
                    WHERE listing_file.user_removed = 0
                    GROUP BY listing_id
                ) AS listingWithMinSort
                     ON true
                         && listing_file.listing_id = listingWithMinSort.listing_id
                         && listing_file.sort = listingWithMinSort.minSort
                         && listing_file.user_removed = 0
            ) AS listingMainImage
            ON listing.id = listingMainImage.listing_id
            SET listing.main_image = listingMainImage.main_image
            WHERE NOT listing.main_image <=> listingMainImage.main_image
        ');

        $this->logger->debug('set main image executed');
    }
}
