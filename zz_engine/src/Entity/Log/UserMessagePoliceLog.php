<?php

declare(strict_types=1);

namespace App\Entity\Log;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="zzzz_police_log_user_message")
 */
class UserMessagePoliceLog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $datetime;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $sourceIp;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $destinationIp;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $text;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true, options={"unsigned"=true})
     */
    private $listingId;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true, options={"unsigned"=true})
     */
    private $userId;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true, options={"unsigned"=true})
     */
    private $userMessageId;

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

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getUserMessageId(): int
    {
        return $this->userMessageId;
    }

    public function setUserMessageId(int $userMessageId): void
    {
        $this->userMessageId = $userMessageId;
    }
}
