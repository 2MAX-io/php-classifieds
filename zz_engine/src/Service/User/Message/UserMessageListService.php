<?php

declare(strict_types=1);

namespace App\Service\User\Message;

use App\Entity\Listing;
use App\Entity\User;
use App\Entity\UserMessage;
use App\Entity\UserMessageThread;
use App\Helper\DateHelper;
use App\Security\CurrentUserService;
use App\Service\User\Message\Dto\UserMessageThreadDto;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

class UserMessageListService
{
    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(CurrentUserService $currentUserService, EntityManagerInterface $em)
    {
        $this->currentUserService = $currentUserService;
        $this->em = $em;
    }

    /**
     * @return UserMessage[]
     */
    public function getMessageListForThread(UserMessageThread $userMessageThread): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('msg');
        $qb->from(UserMessage::class, 'msg');
        $qb->andWhere($qb->expr()->eq('msg.userMessageThread', ':userMessageThread'));
        $qb->setParameter(':userMessageThread', $userMessageThread->getId());

        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->eq('msg.senderUser', ':userId'),
            $qb->expr()->eq('msg.recipientUser', ':userId'),
        ));
        $qb->setParameter(':userId', $this->currentUserService->getUser()->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * @return UserMessageThreadDto[]
     */
    public function getThreadList(User $user): array
    {
        /** @var Connection|\PDO $pdo */
        $pdo = $this->em->getConnection();
        $stmt = $pdo->prepare('
            SELECT
                user_message_thread.id AS userMessageThreadId,
                user_message.sender_user_id AS senderUserId,
                user_message.recipient_user_id AS recipientUserId,
                user_recipient.display_username AS recipientUserName,
                user_sender.display_username AS senderUserName,
                listing.id AS listingId,
                listing.slug AS listingSlug,
                listing.title AS listingTitle,
                MAX(user_message.datetime) AS datetimeString,
                unread_count AS unreadCount,
                null
            FROM user_message_thread
            JOIN user_message ON user_message.user_message_thread_id = user_message_thread.id
            JOIN listing ON user_message_thread.listing_id = listing.id
            JOIN user AS user_recipient ON user_message.recipient_user_id = user_recipient.id
            JOIN user AS user_sender ON user_message.sender_user_id = user_sender.id
            LEFT JOIN (
                SELECT 
                    user_message_thread_id,
                    COUNT(1) AS unread_count,
                    null
                FROM user_message
                WHERE true
                    && user_message.recipient_read = 0
                    && user_message.recipient_user_id = :user_id
            ) AS unread_count ON user_message_thread.id = unread_count.user_message_thread_id
            WHERE true 
            && (
                user_message.sender_user_id = :user_id
                || user_message.recipient_user_id = :user_id
            )
            GROUP BY user_message_thread.id
            ORDER BY user_message_thread.latest_message_datetime DESC
            LIMIT 100
        ');
        $stmt->bindValue(':user_id', $user->getId());
        $stmt->setFetchMode(\PDO::FETCH_CLASS, UserMessageThreadDto::class);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    public function getExistingUserThreadForListing(Listing $listing, User $user): ?UserMessageThread
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('user_message_thread');
        $qb->from(UserMessageThread::class, 'user_message_thread');

        $qb->andWhere($qb->expr()->eq('user_message_thread.listing', ':listing'));
        $qb->setParameter(':listing', $listing->getId());

        $qb->andWhere($qb->expr()->eq('user_message_thread.createdByUser', ':user'));
        $qb->setParameter(':user', $user->getId());

        $qb->addOrderBy('user_message_thread.id', Criteria::DESC);
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param UserMessage[] $userMessageList
     */
    public function markReadByRecipient(array $userMessageList): void
    {
        $currentDatetime = DateHelper::create();
        foreach ($userMessageList as $userMessage) {
            if (!$userMessage->getIsUserRecipient($this->currentUserService->getUser())) {
                continue; // skip, current user not recipient
            }
            if ($userMessage->getRecipientRead()) {
                continue; // skip, already marked as seen
            }

            $userMessage->setRecipientRead(true);
            $userMessage->setRecipientReadDatetime($currentDatetime);
            $this->em->persist($userMessage);
        }
    }
}
