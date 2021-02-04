<?php

declare(strict_types=1);

namespace App\Service\User\Message;

use App\Entity\Log\UserMessagePoliceLog;
use App\Entity\UserMessage;
use App\Entity\UserMessageThread;
use App\Form\User\Message\Dto\SendUserMessageDto;
use App\Helper\DateHelper;
use App\Helper\ServerHelper;
use App\Security\CurrentUserService;
use App\Service\User\Message\Messenger\SendNotification\SendNotificationMessage;
use App\System\Messenger\MessengerHelperService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserMessageSendService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var LoggerInterface
     */
    private $logger;

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

    public function __construct(
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

        if ($this->messengerHelperService->countMessages(SendNotificationMessage::class) < 3) {
            $this->messageBus->dispatch(new SendNotificationMessage());
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
        $listingUrl = $this->urlGenerator->generate(
            'app_listing_show',
            [
                'id' => $listing->getId(),
                'slug' => $listing->getSlug(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $realIpBehindProxy = '';
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $realIpBehindProxy = $_SERVER['HTTP_X_FORWARDED_FOR'] . ' --> ';
        }
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $realIpBehindProxy = $_SERVER['HTTP_CF_CONNECTING_IP'] . ' --> ' . $_SERVER['HTTP_CF_VISITOR'] ?? '';
        }

        $log = new UserMessagePoliceLog();
        $log->setSourceIp($_SERVER['REMOTE_ADDR']);
        $log->setDestinationIp($_SERVER['SERVER_ADDR']);
        $log->setDatetime(\DateTime::createFromFormat('U', (string) $_SERVER['REQUEST_TIME']));
        $log->setListingId($listing->getId());
        $log->setUserMessageId($userMessage->getId());

        $userEmail = '';
        $user = $this->currentUserService->getUserOrNull();
        if ($user) {
            $log->setUserId($user->getId());
            $userEmail = $user->getEmail();
        }

        $requestTimeString = DateHelper::fromMicroTimeFloat($_SERVER['REQUEST_TIME_FLOAT'])->format('Y-m-d H:i:s.u P');
        $currentServerTime = DateHelper::fromMicroTimeFloat(\microtime(true))->format('Y-m-d H:i:s.u P');

        $logText = <<<END
Connection:
{$realIpBehindProxy}{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} --> {$_SERVER['SERVER_ADDR']}:{$_SERVER['SERVER_PORT']}

Request time: {$requestTimeString}
Unix request time float: {$_SERVER['REQUEST_TIME_FLOAT']}
Server time when saving this log: {$currentServerTime}

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
