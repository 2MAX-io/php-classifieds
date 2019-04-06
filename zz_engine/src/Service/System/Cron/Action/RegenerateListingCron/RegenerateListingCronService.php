<?php

declare(strict_types=1);

namespace App\Service\System\Cron\Action\RegenerateListingCron;

use App\Enum\AppCacheEnum;
use App\Service\System\Cron\Action\Base\CronActionInterface;
use App\Service\System\Cron\Action\Base\CronAtNightInterface;
use App\Service\System\Cron\Dto\CronDto;
use App\Service\System\Maintenance\RegenerateListing\Dto\RegenerateListingDto;
use App\Service\System\Maintenance\RegenerateListing\RegenerateListingService;
use App\Service\System\RunEvery\RunEveryService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @see RegenerateListingMessage
 */
class RegenerateListingCronService implements CronActionInterface, CronAtNightInterface, MessageHandlerInterface
{
    /**
     * @var RegenerateListingService
     */
    private $regenerateListingService;

    /**
     * @var RunEveryService
     */
    private $runEveryService;

    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    public function __construct(
        RegenerateListingService $regenerateListingService,
        RunEveryService $runEveryService,
        MessageBusInterface $messageBus
    ) {
        $this->regenerateListingService = $regenerateListingService;
        $this->runEveryService = $runEveryService;
        $this->messageBus = $messageBus;
    }

    public function __invoke(RegenerateListingMessage $regenerateListingMessage): void
    {
        $regenerateListingDto = new RegenerateListingDto();
        $regenerateListingDto->setTimeLimitSeconds(36);
        $this->regenerateListingService->regenerate($regenerateListingDto);
    }

    public function runFromCron(CronDto $cronDto): void
    {
        if (!$cronDto->getIgnoreDelay() && !$this->runEveryService->canRunAgain(AppCacheEnum::CRON_REGENERATE_LISTING, 1800)) {
            return;
        }

        $this->messageBus->dispatch(new RegenerateListingMessage());
    }
}
