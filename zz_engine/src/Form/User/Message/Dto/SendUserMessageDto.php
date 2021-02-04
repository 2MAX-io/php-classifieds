<?php

declare(strict_types=1);

namespace App\Form\User\Message\Dto;

use App\Entity\Listing;
use App\Entity\User;
use App\Entity\UserMessageThread;

class SendUserMessageDto
{
    /**
     * @var string|null
     */
    private $message;

    /**
     * @var Listing|null
     */
    private $listing;

    /**
     * @var User|null
     */
    private $currentUser;

    /**
     * @var bool|null
     */
    private $createThread;

    /**
     * @var UserMessageThread|null
     */
    private $userMessageThread;

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

    public function getCurrentUser(): ?User
    {
        return $this->currentUser;
    }

    public function setCurrentUser(?User $currentUser): void
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
        return $this->userMessageThread;
    }

    public function setUserMessageThread(?UserMessageThread $userMessageThread): void
    {
        $this->userMessageThread = $userMessageThread;
    }
}
