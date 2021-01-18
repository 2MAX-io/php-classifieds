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
    private $userMessageId;

    /**
     * @var int|null
     */
    private $senderUserId;

    /**
     * @var int|null
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
     * @var string|\DateTimeInterface
     */
    private $datetime;

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
    }

    public function __construct()
    {
        $this->userMessageId = (int) $this->userMessageId;
        $this->listingId = (int) $this->listingId;
        $this->senderUserId = (int) $this->senderUserId;
        $this->recipientUserId = (int) $this->recipientUserId;
        $this->datetime = DateHelper::createFromSqlString($this->datetime);
    }

    public function getUserMessageId(): ?int
    {
        return $this->userMessageId;
    }

    public function getSenderUserId(): ?int
    {
        return $this->senderUserId;
    }

    public function getRecipientUserId(): ?int
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
}
