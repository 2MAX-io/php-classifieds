<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table()
 */
class UserMessage
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $senderUser;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $recipientUser;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Listing")
     * @ORM\JoinColumn(nullable=true)
     */
    private $listing;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $datetime;

    /**
     * @ORM\Column(type="string", length=3600, nullable=false)
     */
    private $message;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $notSpamChecked = false;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $spam = false;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $recipientNotified = false;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $recipientSeen = false;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $recipientSeenDatetime;

    public function getAllUsersArray(): array
    {
        return [$this->getSenderUser(), $this->getRecipientUser()];
    }

    public function getIsUserSender(User $user): bool
    {
        return $this->getSenderUser()->getId() === $user->getId();
    }

    public function getOtherUser(User $user): User
    {
        if ($user->getId() === $this->getSenderUser()->getId()) {
            return $this->getRecipientUser();
        }
        if ($user->getId() === $this->getRecipientUser()->getId()) {
            return $this->getSenderUser();
        }

        throw new \RuntimeException('other user not found for ' . $user);
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getSenderUser(): ?User
    {
        return $this->senderUser;
    }

    public function setSenderUser(?User $senderUser): self
    {
        $this->senderUser = $senderUser;

        return $this;
    }

    public function getRecipientUser(): ?User
    {
        return $this->recipientUser;
    }

    public function setRecipientUser(?User $recipientUser): self
    {
        $this->recipientUser = $recipientUser;

        return $this;
    }

    public function getListing(): ?Listing
    {
        return $this->listing;
    }

    public function setListing(?Listing $listing): self
    {
        $this->listing = $listing;

        return $this;
    }

    public function isNotSpamChecked(): bool
    {
        return $this->notSpamChecked;
    }

    public function setNotSpamChecked(bool $notSpamChecked): void
    {
        $this->notSpamChecked = $notSpamChecked;
    }

    public function isSpam(): bool
    {
        return $this->spam;
    }

    public function setSpam(bool $spam): void
    {
        $this->spam = $spam;
    }

    public function isRecipientNotified(): bool
    {
        return $this->recipientNotified;
    }

    public function setRecipientNotified(bool $recipientNotified): void
    {
        $this->recipientNotified = $recipientNotified;
    }

    public function isRecipientSeen(): bool
    {
        return $this->recipientSeen;
    }

    public function setRecipientSeen(bool $recipientSeen): void
    {
        $this->recipientSeen = $recipientSeen;
    }

    public function getRecipientSeenDatetime(): ?\DateTimeInterface
    {
        return $this->recipientSeenDatetime;
    }

    public function setRecipientSeenDatetime(?\DateTimeInterface $recipientSeenDatetime): void
    {
        $this->recipientSeenDatetime = $recipientSeenDatetime;
    }
}
