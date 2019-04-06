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
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=false)
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $senderUser;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $recipientUser;

    /**
     * @var UserMessageThread
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\UserMessageThread")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userMessageThread;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $datetime;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=3600, nullable=false)
     */
    private $message;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $spamChecked = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $spam = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $recipientNotified = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $recipientRead = false;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $recipientReadDatetime;

    /**
     * @return User[]
     */
    public function getAllUsersArray(): array
    {
        return [$this->getSenderUser(), $this->getRecipientUser()];
    }

    public function getIsUserSender(User $user): bool
    {
        return $this->getSenderUser()->getId() === $user->getId();
    }

    public function getIsUserRecipient(User $user): bool
    {
        return $this->getRecipientUser()->getId() === $user->getId();
    }

    public function getOtherUser(User $user): User
    {
        if ($user->getId() === $this->getSenderUser()->getId()) {
            return $this->getRecipientUser();
        }
        if ($user->getId() === $this->getRecipientUser()->getId()) {
            return $this->getSenderUser();
        }

        throw new \RuntimeException("other user not found for user message {$this->getId()} for user {$user->getId()}");
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

    public function getSenderUser(): User
    {
        return $this->senderUser;
    }

    public function setSenderUser(?User $senderUser): self
    {
        $this->senderUser = $senderUser;

        return $this;
    }

    public function getRecipientUser(): User
    {
        return $this->recipientUser;
    }

    public function setRecipientUser(?User $recipientUser): self
    {
        $this->recipientUser = $recipientUser;

        return $this;
    }

    public function isSpamChecked(): bool
    {
        return $this->spamChecked;
    }

    public function setSpamChecked(bool $spamChecked): void
    {
        $this->spamChecked = $spamChecked;
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

    public function getRecipientRead(): bool
    {
        return $this->recipientRead;
    }

    public function setRecipientRead(bool $recipientRead): void
    {
        $this->recipientRead = $recipientRead;
    }

    public function getRecipientReadDatetime(): ?\DateTimeInterface
    {
        return $this->recipientReadDatetime;
    }

    public function setRecipientReadDatetime(?\DateTimeInterface $recipientReadDatetime): void
    {
        $this->recipientReadDatetime = $recipientReadDatetime;
    }

    public function getUserMessageThread(): UserMessageThread
    {
        return $this->userMessageThread;
    }

    public function setUserMessageThread(UserMessageThread $userMessageThread): void
    {
        $this->userMessageThread = $userMessageThread;
    }

    public function getSpamChecked(): ?bool
    {
        return $this->spamChecked;
    }

    public function getSpam(): ?bool
    {
        return $this->spam;
    }

    public function getRecipientNotified(): ?bool
    {
        return $this->recipientNotified;
    }
}
