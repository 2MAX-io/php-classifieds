<?php

declare(strict_types=1);

namespace App\Service\User\Message\SendNotification\Dto;

use App\Entity\User;
use App\Entity\UserMessage;

class MessageToUserAggregateDto
{
    /**
     * @var User
     */
    private $recipientUser;

    /**
     * @var UserMessage[]
     */
    private $userMessageList;

    public function getNewestUserMessage(): UserMessage
    {
        return end($this->userMessageList);
    }

    public function getUserMessageList(): array
    {
        return $this->userMessageList;
    }

    public function setUserMessageList(array $userMessageList): void
    {
        $this->userMessageList = $userMessageList;
    }

    public function addUserMessage(UserMessage $userMessage): void
    {
        $this->userMessageList[] = $userMessage;
    }

    public function getRecipientUser(): User
    {
        return $this->recipientUser;
    }

    public function setRecipientUser(User $recipientUser): void
    {
        $this->recipientUser = $recipientUser;
    }
}
