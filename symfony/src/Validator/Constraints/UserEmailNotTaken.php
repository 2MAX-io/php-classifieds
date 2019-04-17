<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Entity\User;

class UserEmailNotTaken extends UniqueValue
{
    public $message = 'This email is already used by another user.';
    public $entityClass = User::class;
    public $fields = ['email'];

    public function getRequiredOptions(): array
    {
        return [];
    }
}
