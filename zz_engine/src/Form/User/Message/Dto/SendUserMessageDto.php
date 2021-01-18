<?php

declare(strict_types=1);

namespace App\Form\User\Message\Dto;

use App\Entity\Listing;
use App\Entity\User;
use App\Entity\UserMessage;

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
     * @var UserMessage|null
     */
    private $userMessage;

    /**
     * @var User|null
     */
    private $currentUser;

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

    public function getUserMessage(): ?UserMessage
    {
        return $this->userMessage;
    }

    public function setUserMessage(?UserMessage $userMessage): void
    {
        $this->userMessage = $userMessage;
    }
}
