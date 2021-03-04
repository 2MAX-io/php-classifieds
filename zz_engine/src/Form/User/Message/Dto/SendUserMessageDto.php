<?php

declare(strict_types=1);

namespace App\Form\User\Message\Dto;

use App\Entity\Listing;
use App\Entity\User;
use App\Entity\UserMessageThread;

class SendUserMessageDto
{
    /**
     * @var null|string
     */
    private $message;

    /**
     * @var null|Listing
     */
    private $listing;

    /**
     * @var User
     */
    private $currentUser;

    /**
     * @var null|UserMessageThread
     */
    private $userMessageThread;

    /**
     * @var null|bool
     */
    private $createThread;

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    public function getListing(): ?Listing
    {
        return $this->listing;
    }

    public function setListing(?Listing $listing): void
    {
        $this->listing = $listing;
    }

    public function getCurrentUser(): User
    {
        return $this->currentUser;
    }

    public function setCurrentUser(User $currentUser): void
    {
        $this->currentUser = $currentUser;
    }

    public function getCreateThread(): ?bool
    {
        return $this->createThread;
    }

    public function setCreateThread(?bool $createThread): void
    {
        $this->createThread = $createThread;
    }

    public function getUserMessageThread(): ?UserMessageThread
    {
        return $this->userMessageThread;
    }

    public function getUserMessageThreadNotNull(): UserMessageThread
    {
        if (null === $this->userMessageThread) {
            throw new \RuntimeException('userMessageThread is null');
        }

        return $this->userMessageThread;
    }

    public function setUserMessageThread(?UserMessageThread $userMessageThread): void
    {
        $this->userMessageThread = $userMessageThread;
    }
}
