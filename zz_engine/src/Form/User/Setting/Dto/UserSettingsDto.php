<?php

declare(strict_types=1);

namespace App\Form\User\Setting\Dto;

use App\Entity\User;

class UserSettingsDto
{
    /**
     * @var null|string
     */
    public $displayUsername;

    public static function fromUser(User $user): self
    {
        $userSettingsDto = new self();
        $userSettingsDto->setDisplayUsername($user->getDisplayUsername());

        return $userSettingsDto;
    }

    public function getDisplayUsername(): ?string
    {
        return $this->displayUsername;
    }

    public function setDisplayUsername(?string $displayUsername): void
    {
        $this->displayUsername = $displayUsername;
    }
}
