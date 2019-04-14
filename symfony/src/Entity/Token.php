<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TokenRepository")
 * @ORM\Table(name="zzzz_token")
 */
class Token
{
    public const USER_EMAIL_CHANGE_TYPE = 'USER_EMAIL_CHANGE_TYPE';
    public const USER_PASSWORD_CHANGE_TYPE = 'USER_PASSWORD_CHANGE_TYPE';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $token;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=5000, nullable=true)
     */
    private $valueMain;

    /**
     * @ORM\Column(type="datetimetz", nullable=false)
     */
    private $createdDate;

    /**
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $validUntilDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getValueMain(): ?string
    {
        return $this->valueMain;
    }

    public function setValueMain(?string $valueMain): self
    {
        $this->valueMain = $valueMain;

        return $this;
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->createdDate;
    }

    public function setCreatedDate(\DateTimeInterface $createdDate): self
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    public function getValidUntilDate(): ?\DateTimeInterface
    {
        return $this->validUntilDate;
    }

    public function setValidUntilDate(?\DateTimeInterface $validUntilDate): self
    {
        $this->validUntilDate = $validUntilDate;

        return $this;
    }
}
