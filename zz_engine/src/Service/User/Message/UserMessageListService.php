<?php

declare(strict_types=1);

namespace App\Service\User\Message;

use App\Entity\Listing;
use App\Entity\User;
use App\Entity\UserMessage;
use App\Security\CurrentUserService;
use App\Service\User\Message\Dto\UserMessageThreadDto;
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
    public function getMessageListForUser(Listing $listing, User $user): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('msg');
        $qb->from(UserMessage::class, 'msg');
        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->eq('msg.senderUser', ':user_id'),
            $qb->expr()->eq('msg.recipientUser', ':user_id'),
        ));
        $qb->andWhere($qb->expr()->eq('msg.listing', ':listing_id'));
        $qb->setParameter(':user_id', $user->getId());
        $qb->setParameter(':listing_id', $listing->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * @return UserMessageThreadDto[]
     */
    public function getThreadsForUser(User $user): array
    {
        /** @var \PDO $pdo */
        $pdo = $this->em->getConnection();
        $stmt = $pdo->prepare(
            /** @lang MySQL */ '
            SELECT
                user_message.id AS userMessageId,
                user_message.sender_user_id AS senderUserId,
                user_message.recipient_user_id AS recipientUserId,
                user_recipient.display_username AS recipientUserName,
                user_sender.display_username AS senderUserName,
                listing.id AS listingId,
                listing.slug AS listingSlug,
                listing.title AS listingTitle,
                MAX(user_message.datetime) AS datetime,
                null
            FROM user_message
            JOIN listing ON user_message.listing_id = listing.id
            JOIN user AS user_recipient ON user_message.recipient_user_id = user_recipient.id
            JOIN user AS user_sender ON user_message.sender_user_id = user_sender.id
            WHERE true 
            && (
                user_message.sender_user_id = :user_id
                || user_message.recipient_user_id = :user_id
            )
            GROUP BY user_message.listing_id
            ORDER BY user_message.datetime DESC
        ');
        $stmt->bindValue(':user_id', $user->getId());
        $stmt->setFetchMode(\PDO::FETCH_CLASS, UserMessageThreadDto::class);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
