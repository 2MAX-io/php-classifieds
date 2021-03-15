<?php

declare(strict_types=1);

namespace App\Service\User\Message\Dto;

use App\Entity\User;
use App\Helper\DateHelper;

class UserMessageThreadDto
{
    /**
     * @var int|null
     */
    private $userMessageThreadId;

    /**
     * @var int
     */
    private $senderUserId;

    /**
     * @var int
     */
    private $recipientUserId;

    /**
     * @var string|null
     */
    private $recipientUserName;

    /**
     * @var string|null
     */
    private $senderUserName;

    /**
     * @var int|null
     */
    private $listingId;

    /**
     * @var string|null
     */
    private $listingSlug;

    /**
     * @var string|null
     */
    private $listingTitle;

    /**
     * @var int|null
     */
    private $unreadCount;

    /**
     * @var string
     */
    private $datetimeString;

    /**
     * @var \DateTimeInterface
     */
    private $datetime;

    /** @noinspection UnnecessaryCastingInspection */
    public function __construct()
    {
        $this->userMessageThreadId = (int) $this->userMessageThreadId;
        $this->unreadCount = (int) $this->unreadCount;
        $this->listingId = (int) $this->listingId;
        $this->senderUserId = (int) $this->senderUserId;
        $this->recipientUserId = (int) $this->recipientUserId;
        $this->datetime = DateHelper::createFromSqlString($this->datetimeString);
    }

    public function getReplyToName(User $currentUser): ?string
    {
        if ($currentUser->getId() === $this->getSenderUserId()) {
            return $this->getRecipientUserName();
        }
        if ($currentUser->getId() === $this->getRecipientUserId()) {
            return $this->getSenderUserName();
        }

        return null;
    }

    public function getReplyToUserId(User $currentUser): int
    {
        if ($currentUser->getId() === $this->getSenderUserId()) {
            return $this->getRecipientUserId();
        }
        if ($currentUser->getId() === $this->getRecipientUserId()) {
            return $this->getSenderUserId();
        }

        throw new \RuntimeException('reply to user id not found');
    }

    public function getUserMessageThreadId(): ?int
    {
        return $this->userMessageThreadId;
    }

    public function setUserMessageThreadId(?int $userMessageThreadId): void
    {
        $this->userMessageThreadId = $userMessageThreadId;
    }

    public function getSenderUserId(): int
    {
        return $this->senderUserId;
    }

    public function getRecipientUserId(): int
    {
        return $this->recipientUserId;
    }

    public function getListingId(): ?int
    {
        return $this->listingId;
    }

    public function getListingTitle(): ?string
    {
        return $this->listingTitle;
    }

    public function getDatetime(): \DateTimeInterface
    {
        return $this->datetime;
    }

    public function getRecipientUserName(): ?string
    {
        return $this->recipientUserName;
    }

    public function getSenderUserName(): ?string
    {
        return $this->senderUserName;
    }

    public function getListingSlug(): ?string
    {
        return $this->listingSlug;
    }

    public function getUnreadCount(): ?int
    {
        return $this->unreadCount;
    }

    public function setUnreadCount(?int $unreadCount): void
    {
        $this->unreadCount = $unreadCount;
    }
}
