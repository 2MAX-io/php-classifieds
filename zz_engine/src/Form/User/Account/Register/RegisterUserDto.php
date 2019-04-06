<?php

declare(strict_types=1);

namespace App\Form\User\Account\Register;

class RegisterUserDto
{
    /** @var string|null */
    private $email;

    /** @var string|null */
    private $password;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }
}
