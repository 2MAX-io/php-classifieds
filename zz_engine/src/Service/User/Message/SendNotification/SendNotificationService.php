<?php

declare(strict_types=1);

namespace App\Service\User\Message\SendNotification;

use App\Entity\UserMessage;
use App\Service\Email\EmailService;
use App\Service\User\Message\Messenger\SendNotification\SendNotificationMessage;
use App\Service\User\Message\SendNotification\Dto\MessageToUserAggregateDto;
use App\System\Messenger\MessengerHelperService;
use Carbon\Carbon;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Contracts\Translation\TranslatorInterface;

class SendNotificationService
{
    public const RETRY_NOTIFICATION_TIME_SECONDS = 5 * 60;
    public const NOTIFICATION_DELAY_SECONDS = 5 * 60;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TranslatorInterface
     */
    private $trans;

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    /**
     * @var MessengerHelperService
     */
    private $messengerHelperService;

    public function __construct(
        EmailService $emailService,
        MessengerHelperService $messengerHelperService,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        MessageBusInterface $messageBus,
        TranslatorInterface $trans
    ) {
        $this->em = $em;
        $this->trans = $trans;
        $this->mailer = $mailer;
        $this->emailService = $emailService;
        $this->messageBus = $messageBus;
        $this->messengerHelperService = $messengerHelperService;
    }

    public function sendNotifications(): void
    {
        $atLeastOneNotificationSend = false;
        foreach ($this->getAggregatedMessagesToNotifyList() as $aggregatedMessage) {
            $atLeastOneNotificationSend = true;
            $this->sendEmail($aggregatedMessage);
        }

        if (!$atLeastOneNotificationSend) {
            $this->noNotificationSendTryAgainLater();
        }
    }

    private function sendEmail(MessageToUserAggregateDto $messageToUserAggregateDto): void
    {
        $newestUserMessage = $messageToUserAggregateDto->getNewestUserMessage();
        $listing = $newestUserMessage->getUserMessageThread()->getListing();

        $email = $this->emailService->getTemplatedEmail();
        $email->subject($this->trans->trans('trans.Private message for listing: {listingTitle}', [
            '{listingTitle}' => $listing->getTitle(),
        ]));
        $email->to($messageToUserAggregateDto->getRecipientUser()->getEmail());
        $email->htmlTemplate('email/user_message/notification_new_user_message.html.twig');
        $email->context([
            'listing' => $listing,
            'newestUserMessage' => $newestUserMessage,
            'userMessageList' => $messageToUserAggregateDto->getUserMessageList(),
        ]);
        $this->mailer->send($email);

        foreach ($messageToUserAggregateDto->getUserMessageList() as $userMessage) {
            $userMessage->setRecipientNotified(true);
            $this->em->persist($userMessage);
        }
        $this->em->flush();
    }

    /**
     * @return MessageToUserAggregateDto[]
     */
    private function getAggregatedMessagesToNotifyList(): array
    {
        $returnList = [];
        $currentThread = null;
        $messageToUserAggregateDto = null;
        foreach ($this->getUserMessagesToNotify() as $userMessage) {
            if ($currentThread !== $userMessage->getUserMessageThread()) {
                $currentThread = $userMessage->getUserMessageThread();
                $messageToUserAggregateDto = new MessageToUserAggregateDto();
                $messageToUserAggregateDto->setRecipientUser($userMessage->getRecipientUser());
                $returnList[] = $messageToUserAggregateDto;
            }
            $messageToUserAggregateDto->addUserMessage($userMessage);
        }

        return $returnList;
    }

    /**
     * @return UserMessage[]
     */
    private function getUserMessagesToNotify(): array
    {
        $userMessageIdList = $this->getLastMessageForUserOlderThanIdList(
            Carbon::now()->subSeconds(self::NOTIFICATION_DELAY_SECONDS),
        );

        $qb = $this->em->createQueryBuilder();
        $qb->select('msg');
        $qb->addSelect('thread');
        $qb->from(UserMessage::class, 'msg');
        $qb->join('msg.userMessageThread', 'thread');
        $qb->andWhere($qb->expr()->in('msg.id', ':userMessageIdList'));
        $qb->setParameter(':userMessageIdList', $userMessageIdList);
        $qb->andWhere($qb->expr()->eq('msg.recipientNotified', 0));
        $qb->andWhere($qb->expr()->eq('msg.recipientRead', 0));

        $qb->addOrderBy('msg.userMessageThread');
        $qb->addOrderBy('msg.datetime', Criteria::ASC);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return int[]
     */
    private function getLastMessageForUserOlderThanIdList(\DateTimeInterface $olderThanDatetime): array
    {
        /** @var \PDO $pdo */
        $pdo = $this->em->getConnection();
        $stmt = $pdo->prepare('
SELECT
    user_message_to_notify.id,
    null
FROM user_message AS user_message_to_notify
JOIN user_message AS newest_user_message_in_thread
ON true
    && user_message_to_notify.user_message_thread_id = newest_user_message_in_thread.user_message_thread_id
JOIN (
    SELECT
        MAX(user_message.id) AS id,
        null
    FROM user_message
    WHERE true
              && user_message.recipient_notified = 0
              && user_message.recipient_read = 0
    GROUP BY user_message_thread_id
) newest_user_message_in_thread_inner
ON true
    && newest_user_message_in_thread.id = newest_user_message_in_thread_inner.id
    && newest_user_message_in_thread.datetime < :olderThan # last message in group is older than NOTIFICATION_DELAY_SECONDS
WHERE true
    && user_message_to_notify.recipient_notified = 0
    && user_message_to_notify.recipient_read = 0
        '
        );
        $stmt->bindValue(':olderThan', $olderThanDatetime);
        $stmt->setFetchMode(\PDO::FETCH_COLUMN, 0);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    private function noNotificationSendTryAgainLater(): void
    {
        $noMessagesWaitForNotification = $this->countMessagesWaitingForNotification() < 1;
        if ($noMessagesWaitForNotification) {
            return;
        }

        if ($this->messengerHelperService->countMessages(SendNotificationMessage::class)) {
            return;
        }

        $this->messageBus->dispatch(
            new SendNotificationMessage(),
            [
                new DelayStamp(self::RETRY_NOTIFICATION_TIME_SECONDS * 1000),
            ]
        );
    }

    private function countMessagesWaitingForNotification(): int
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select($qb->expr()->count(1));
        $qb->from(UserMessage::class, 'userMessage');
        $qb->andWhere($qb->expr()->eq('userMessage.recipientNotified', 0));

        return (int) $qb->getQuery()->getScalarResult();
    }
}
