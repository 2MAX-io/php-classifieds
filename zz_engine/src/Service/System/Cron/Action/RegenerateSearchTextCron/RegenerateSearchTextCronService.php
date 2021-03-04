<?php

declare(strict_types=1);

namespace App\Service\System\Cron\Action\RegenerateSearchTextCron;

use App\Enum\AppCacheEnum;
use App\Service\System\Cron\Action\Base\CronActionInterface;
use App\Service\System\Cron\Action\Base\CronAtNightInterface;
use App\Service\System\Cron\Dto\CronDto;
use App\Service\System\Maintenance\RegenerateSearchText\Dto\RegenerateSearchTextDto;
use App\Service\System\Maintenance\RegenerateSearchText\RegenerateSearchTextService;
use App\Service\System\RunEvery\RunEveryService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @see RegenerateSearchTextMessage
 */
class RegenerateSearchTextCronService implements CronActionInterface, CronAtNightInterface, MessageHandlerInterface
{
    /**
     * @var RegenerateSearchTextService
     */
    private $regenerateSearchTextService;

    /**
     * @var RunEveryService
     */
    private $runEveryService;

    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    public function __construct(
        RegenerateSearchTextService $regenerateSearchTextService,
        RunEveryService $runEveryService,
        MessageBusInterface $messageBus
    ) {
        $this->regenerateSearchTextService = $regenerateSearchTextService;
        $this->runEveryService = $runEveryService;
        $this->messageBus = $messageBus;
    }

    public function __invoke(RegenerateSearchTextMessage $regenerateSearchTextMessage): void
    {
        $regenerateSearchTextDto = new RegenerateSearchTextDto();
        $regenerateSearchTextDto->setTimeLimitSeconds(36);
        $this->regenerateSearchTextService->regenerate($regenerateSearchTextDto);
    }

    public function runFromCron(CronDto $cronDto): void
    {
        if (!$cronDto->getIgnoreDelay() && !$this->runEveryService->canRunAgain(AppCacheEnum::CRON_REGENERATE_SEARCH_TEXT, 1800)) {
            return;
        }

        $this->messageBus->dispatch(new RegenerateSearchTextMessage());
    }
}
