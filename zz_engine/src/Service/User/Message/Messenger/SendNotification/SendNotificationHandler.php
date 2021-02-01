<?php

declare(strict_types=1);

namespace App\Service\User\Message\Messenger\SendNotification;

use App\Service\User\Message\SendNotification\SendNotificationService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * @see SendNotificationMessage
 */
class SendNotificationHandler implements MessageHandlerInterface
{
    /**
     * @var SendNotificationService
     */
    private $sendNotificationService;

    public function __construct(SendNotificationService $sendNotificationService)
    {
        $this->sendNotificationService = $sendNotificationService;
    }

    public function __invoke(SendNotificationMessage $sendNotificationMessage): void
    {
        $this->sendNotificationService->sendNotifications();
    }
}
