<?php

declare(strict_types=1);

namespace App\Service\Listing\Secondary\PoliceLog\Dto;

use App\Helper\DateHelper;

class PoliceLogUserMessageItemDto
{
    /** @var int */
    private $userMessageId;

    /** @var int */
    private $threadId;

    /** @var int|null */
    private $listingId;

    /** @var int|null */
    private $senderUserId;

    /** @var int|null */
    private $recipientUserId;

    /** @var string|null */
    private $listingSlug;

    /** @var string|null */
    private $listingTitle;

    /** @var string */
    private $userMessage;

    /** @var string|null */
    private $sender;

    /** @var string|null */
    private $recipient;

    /** @var string */
    private $logText;

    /** @var \DateTimeInterface */
    private $datetime;

    /** @var string */
    private $datetimeString;

    /** @noinspection UnnecessaryCastingInspection */
    public function __construct()
    {
        $this->userMessageId = (int) $this->userMessageId;
        $this->threadId = (int) $this->threadId;
        $this->listingId = (int) $this->listingId;
        $this->senderUserId = (int) $this->senderUserId;
        $this->recipientUserId = (int) $this->recipientUserId;
        $this->datetime = DateHelper::createFromSqlString($this->datetimeString);
    }

    public function getUserMessageId(): int
    {
        return $this->userMessageId;
    }

    public function getListingId(): ?int
    {
        return $this->listingId;
    }

    public function getListingSlug(): ?string
    {
        return $this->listingSlug;
    }

    public function getListingTitle(): ?string
    {
        return $this->listingTitle;
    }

    public function getUserMessage(): string
    {
        return $this->userMessage;
    }

    public function getLogText(): string
    {
        return $this->logText;
    }

    public function getSender(): ?string
    {
        return $this->sender;
    }

    public function setSender(?string $sender): void
    {
        $this->sender = $sender;
    }

    public function getRecipient(): ?string
    {
        return $this->recipient;
    }

    public function setRecipient(?string $recipient): void
    {
        $this->recipient = $recipient;
    }

    public function getThreadId(): int
    {
        return $this->threadId;
    }

    public function setThreadId(int $threadId): void
    {
        $this->threadId = $threadId;
    }

    public function getSenderUserId(): ?int
    {
        return $this->senderUserId;
    }

    public function setSenderUserId(?int $senderUserId): void
    {
        $this->senderUserId = $senderUserId;
    }

    public function getDatetime(): \DateTimeInterface
    {
        return $this->datetime;
    }

    public function getRecipientUserId(): ?int
    {
        return $this->recipientUserId;
    }
}
