<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="zzzz_token_field")
 */
class TokenField
{
    public const USER_ID_FIELD = 'USER_ID_FIELD';
    public const REMINDED_HASHED_PASSWORD = 'REMINDED_HASHED_PASSWORD';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Token", inversedBy="fields")
     * @ORM\JoinColumn(nullable=false)
     */
    private $token;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $name;

    /**
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
