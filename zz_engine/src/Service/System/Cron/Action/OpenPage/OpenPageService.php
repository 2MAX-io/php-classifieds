<?php

declare(strict_types=1);

namespace App\Service\System\Cron\Action\OpenPage;

use App\Enum\AppCacheEnum;
use App\Service\System\Cron\Action\Base\CronActionInterface;
use App\Service\System\Cron\Dto\CronDto;
use App\Service\System\RunEvery\RunEveryService;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OpenPageService implements CronActionInterface, MessageHandlerInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var RunEveryService
     */
    private $runEveryService;

    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        RunEveryService $runEveryService,
        MessageBusInterface $messageBus
    ) {
        $this->runEveryService = $runEveryService;
        $this->urlGenerator = $urlGenerator;
        $this->messageBus = $messageBus;
    }

    public function __invoke(OpenPageMessage $openPageMessage): void
    {
        $this->openPage();
    }

    public function runFromCron(CronDto $cronDto): void
    {
        if (!$cronDto->getIgnoreDelay() && !$this->runEveryService->canRunAgain(AppCacheEnum::CRON_OPEN_INDEX, 5 * 300)) {
            return;
        }

        $this->messageBus->dispatch(new OpenPageMessage());
    }

    public function openPage(): void
    {
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
