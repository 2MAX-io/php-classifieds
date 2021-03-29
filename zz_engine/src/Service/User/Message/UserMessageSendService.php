<?php

declare(strict_types=1);

namespace App\Service\User\Message;

use App\Entity\Log\PoliceLogUserMessage;
use App\Entity\UserMessage;
use App\Entity\UserMessageThread;
use App\Form\User\Message\Dto\SendUserMessageDto;
use App\Helper\DateHelper;
use App\Helper\ServerHelper;
use App\Security\CurrentUserService;
use App\Service\System\Messenger\MessengerHelperService;
use App\Service\System\SystemLog\PoliceLog\PoliceLogHelperService;
use App\Service\User\Message\Messenger\SendNotification\SendNotificationMessage;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserMessageSendService
{
    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    /**
     * @var MessengerHelperService
     */
    private $messengerHelperService;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var PoliceLogHelperService
     */
    private $policeLogHelperService;

    public function __construct(
        PoliceLogHelperService $policeLogHelperService,
        CurrentUserService $currentUserService,
        MessengerHelperService $messengerHelperService,
        MessageBusInterface $messageBus,
        UrlGeneratorInterface $urlGenerator,
        EntityManagerInterface $em,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->logger = $logger;
        $this->currentUserService = $currentUserService;
        $this->urlGenerator = $urlGenerator;
        $this->messageBus = $messageBus;
        $this->messengerHelperService = $messengerHelperService;
        $this->policeLogHelperService = $policeLogHelperService;
    }

    public function sendMessage(SendUserMessageDto $sendUserMessageDto): void
    {
        $userMessageThread = $sendUserMessageDto->getUserMessageThread();
        $currentDatetime = DateHelper::create();
        if ($sendUserMessageDto->getCreateThread()) {
            $userMessageThread = new UserMessageThread();
            $userMessageThread->setListing($sendUserMessageDto->getListing());
            $userMessageThread->setCreatedByUser($sendUserMessageDto->getCurrentUser());
            $userMessageThread->setCreatedDatetime($currentDatetime);
            $userMessageThread->setLatestMessageDatetime($currentDatetime);
            $sendUserMessageDto->setUserMessageThread($userMessageThread);
        }

        if (null === $userMessageThread) {
            throw new \RuntimeException('can not find thread when sending message');
        }

        $userMessage = new UserMessage();
        $userMessage->setMessage($sendUserMessageDto->getMessage());
        $userMessage->setSenderUser($sendUserMessageDto->getCurrentUser());
        $userMessage->setRecipientUser($userMessageThread->getOtherUser($sendUserMessageDto->getCurrentUser()));
        $userMessage->setUserMessageThread($userMessageThread);
        $userMessage->setDatetime($currentDatetime);
        $userMessageThread->setLatestMessageDatetime($currentDatetime);

        if ($userMessage->getSenderUser()->getId() === $userMessage->getRecipientUser()->getId()) {
            $this->logger->error('sender and recipient should not be the same', [
                'getSenderUser' => $userMessage->getSenderUser()->getId(),
                'getRecipientUser' => $userMessage->getRecipientUser()->getId(),
            ]);

            throw new \RuntimeException('sender and recipient should not be the same');
        }

        if (!$this->allowedToSendMessage($sendUserMessageDto)) {
            $this->logger->error('user can not sent this message', [
                $sendUserMessageDto,
            ]);

            throw new UnauthorizedHttpException('user can not send this message');
        }

        $this->em->persist($userMessageThread);
        $this->em->persist($userMessage);
        $this->em->flush();
        $this->saveUserMessagePoliceLog($userMessage);
        $this->em->flush();

        if ($userMessage->getRecipientUser()->getNotificationByEmailNewMessage()) {
            if ($this->messengerHelperService->countMessages(SendNotificationMessage::class) < 3) {
                $this->messageBus->dispatch(new SendNotificationMessage());
            }
        } else {
            $userMessage->setRecipientNotified(true);
        }
    }

    public function allowedToSendMessage(SendUserMessageDto $sendUserMessageDto): bool
    {
        return \in_array(
            $sendUserMessageDto->getCurrentUser(),
            $sendUserMessageDto->getUserMessageThreadNotNull()->getAllUsersArray(),
            true
        );
    }

    private function saveUserMessagePoliceLog(UserMessage $userMessage): void
    {
        $listing = $userMessage->getUserMessageThread()->getListingNotNull();
        $policeLogData = $this->policeLogHelperService->getPoliceLogData();
        $log = new PoliceLogUserMessage();
        $log->setSourceIp($policeLogData->getSourceIp());
        $log->setSourcePort($policeLogData->getSourcePort());
        $log->setDestinationIp($policeLogData->getDestinationIp());
        $log->setDestinationPort($policeLogData->getDestinationPort());
        $log->setDatetime($policeLogData->getDatetime());
        $log->setListingId($listing->getId());
        $log->setUserMessageId($userMessage->getId());
        $log->setRecipientUserId($userMessage->getRecipientUser()->getId());
        $log->setUserMessageThreadId($userMessage->getUserMessageThread()->getId());

        $listingUrl = $this->urlGenerator->generate(
            'app_listing_show',
            [
                'id' => $listing->getId(),
                'slug' => $listing->getSlug(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $userEmail = '';
        $user = $this->currentUserService->getUserOrNull();
        if ($user) {
            $log->setSenderUserId($user->getId());
            $userEmail = $user->getEmail();
        }

        $logText = <<<END
{$policeLogData->getConnectionDataText()}

Message Details:
    Message sender email: {$userEmail}
    For listing: {$listing->getTitle()}
    Listing URL: {$listingUrl}
    Listing ID: {$listing->getId()}
--- Message:
{$userMessage->getMessage()}
--- Message End


END;

        $logText .= ServerHelper::getServerAsString();
        $log->setText($logText);
        $this->em->persist($log);
    }
}
