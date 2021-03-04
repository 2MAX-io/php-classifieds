<?php

declare(strict_types=1);

namespace App\Entity\Log;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="zzzz_police_log_user_message")
 */
class PoliceLogUserMessage
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=false)
     */
    private $id;

    /**
     * @var null|int
     *
     * @ORM\Column(type="integer", nullable=true, options={"unsigned"=true})
     */
    private $listingId;

    /**
     * @var null|int
     *
     * @ORM\Column(type="integer", nullable=true, options={"unsigned"=true})
     */
    private $senderUserId;

    /**
     * @var null|int
     *
     * @ORM\Column(type="integer", nullable=true, options={"unsigned"=true})
     */
    private $recipientUserId;

    /**
     * @var null|int
     *
     * @ORM\Column(type="integer", nullable=true, options={"unsigned"=true})
     */
    private $userMessageId;

    /**
     * @var null|int
     *
     * @ORM\Column(type="integer", nullable=true, options={"unsigned"=true})
     */
    private $userMessageThreadId;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $datetime;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $sourceIp;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10, nullable=false)
     */
    private $sourcePort;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $destinationIp;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10, nullable=false)
     */
    private $destinationPort;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     */
    private $text;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSourceIp(): ?string
    {
        return $this->sourceIp;
    }

    public function setSourceIp(string $sourceIp): self
    {
        $this->sourceIp = $sourceIp;

        return $this;
    }

    public function getDestinationIp(): ?string
    {
        return $this->destinationIp;
    }

    public function setDestinationIp(string $destinationIp): self
    {
        $this->destinationIp = $destinationIp;

        return $this;
    }

    public function getSourcePort(): string
    {
        return $this->sourcePort;
    }

    public function setSourcePort(string $sourcePort): void
    {
        $this->sourcePort = $sourcePort;
    }

    public function getDestinationPort(): string
    {
        return $this->destinationPort;
    }

    public function setDestinationPort(string $destinationPort): void
    {
        $this->destinationPort = $destinationPort;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getListingId(): ?int
    {
        return $this->listingId;
    }

    public function setListingId(int $listingId): self
    {
        $this->listingId = $listingId;

        return $this;
    }

    public function getSenderUserId(): ?int
    {
        return $this->senderUserId;
    }

    public function setSenderUserId(int $senderUserId): self
    {
        $this->senderUserId = $senderUserId;

        return $this;
    }

    public function getUserMessageId(): ?int
    {
        return $this->userMessageId;
    }

    public function setUserMessageId(?int $userMessageId): void
    {
        $this->userMessageId = $userMessageId;
    }

    public function getUserMessageThreadId(): ?int
    {
        return $this->userMessageThreadId;
    }

    public function setUserMessageThreadId(?int $userMessageThreadId): void
    {
        $this->userMessageThreadId = $userMessageThreadId;
    }

    public function getRecipientUserId(): ?int
    {
        return $this->recipientUserId;
    }

    public function setRecipientUserId(?int $recipientUserId): void
    {
        $this->recipientUserId = $recipientUserId;
    }
}
