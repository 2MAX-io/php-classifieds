<?php

declare(strict_types=1);

namespace App\Service\User\Message;

use App\Entity\UserMessage;
use App\Form\User\Message\Dto\SendUserMessageDto;
use App\Helper\DateHelper;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

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

    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    public function sendMessage(SendUserMessageDto $sendUserMessageDto): void
    {
        if (!$this->allowedToSendMessage($sendUserMessageDto)) {
            $this->logger->error('user can not sent this message', [
                $sendUserMessageDto,
            ]);

            throw new UnauthorizedHttpException('user can not send this message');
        }

        $userMessage = new UserMessage();
        $userMessage->setMessage($sendUserMessageDto->getMessage());
        $userMessage->setSenderUser($sendUserMessageDto->getCurrentUser());

        if ($sendUserMessageDto->getUserMessage()) {
            $userMessage->setRecipientUser($sendUserMessageDto->getUserMessage()->getOtherUser($sendUserMessageDto->getCurrentUser()));
        } else {
            $userMessage->setRecipientUser($sendUserMessageDto->getListing()->getUser());
        }

        $userMessage->setListing($sendUserMessageDto->getListing());
        $userMessage->setDatetime(DateHelper::create());

        if ($userMessage->getSenderUser()->getId() === $userMessage->getRecipientUser()->getId()) {
            $this->logger->error('sender and recipient should not be the same', [
                'getSenderUser' => $userMessage->getSenderUser()->getId(),
                'getRecipientUser' => $userMessage->getRecipientUser()->getId(),
            ]);
        }

        $this->em->persist($userMessage);
    }

    public function allowedToSendMessage(SendUserMessageDto $sendUserMessageDto): bool
    {
        return \in_array(
            $sendUserMessageDto->getCurrentUser(),
            $sendUserMessageDto->getUserMessage()->getAllUsersArray(),
        );
    }
}
