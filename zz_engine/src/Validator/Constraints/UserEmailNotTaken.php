<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Entity\User;

class UserEmailNotTaken extends UniqueValue
{
    /** @var string */
    public $message = 'This email is already used by another user.';

    /** @var string */
    public $entityClass = User::class;

    /** @var string[] */
    public $fields = ['email'];

    public function getRequiredOptions(): array
    {
        return [];
    }
}
