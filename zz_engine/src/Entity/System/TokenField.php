<?php

declare(strict_types=1);

namespace App\Entity\System;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="zzzz_token_field")
 */
class TokenField
{
    public const USER_ID_FIELD = 'USER_ID_FIELD';
    public const REMINDED_HASHED_PASSWORD = 'REMINDED_HASHED_PASSWORD';
    public const USER_EMAIL_FIELD = 'USER_EMAIL_FIELD';
    public const USER_NEW_EMAIL_FIELD = 'USER_NEW_EMAIL_FIELD';
    public const CHANGED_NEW_HASHED_PASSWORD = 'CHANGED_NEW_HASHED_PASSWORD';

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=false)
     */
    private $id;

    /**
     * @var Token
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\System\Token", inversedBy="fields")
     * @ORM\JoinColumn(nullable=false)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     */
    private $value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getToken(): ?Token
    {
        return $this->token;
    }

    public function setToken(?Token $token): self
    {
        $this->token = $token;

        return $this;
    }
}
