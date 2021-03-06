<?php

declare(strict_types=1);

namespace App\Entity\System;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TokenRepository")
 * @ORM\Table(name="zzzz_token")
 */
class Token
{
    public const USER_REGISTER_TYPE = 'USER_REGISTER_TYPE';
    public const USER_EMAIL_CHANGE_TYPE = 'USER_EMAIL_CHANGE_TYPE';
    public const USER_PASSWORD_CHANGE_TYPE = 'USER_PASSWORD_CHANGE_TYPE';
    public const USER_PASSWORD_REMIND = 'USER_PASSWORD_REMIND';

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=false)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=false, unique=true)
     */
    private $tokenString;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $type;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $createdDate;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $used = false;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $validUntilDate;

    /**
     * @var Collection|TokenField[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\System\TokenField", mappedBy="token", indexBy="name", cascade={"all"})
     */
    private $fields;

    public function __construct()
    {
        $this->fields = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection|TokenField[]
     */
    public function getFields(): Collection
    {
        return $this->fields;
    }

    public function addField(TokenField $field): self
    {
        if (!$this->fields->contains($field)) {
            $this->fields[] = $field;
            $field->setToken($this);
        }

        return $this;
    }

    public function removeField(TokenField $field): self
    {
        if ($this->fields->contains($field)) {
            $this->fields->removeElement($field);
            // set the owning side to null (unless already changed)
            if ($field->getToken() === $this) {
                $field->setToken(null);
            }
        }

        return $this;
    }

    public function getFieldByName(string $fieldName): ?string
    {
        /** @var TokenField|null $field */
        $field = $this->getFields()->get($fieldName);

        if (null === $field) {
            return null;
        }

        return $field->getValue();
    }

    public function getTokenString(): ?string
    {
        return $this->tokenString;
    }

    public function setTokenString(string $tokenString): self
    {
        $this->tokenString = $tokenString;

        return $this;
    }

    public function getUsed(): ?bool
    {
        return $this->used;
    }

    public function setUsed(bool $used): self
    {
        $this->used = $used;

        return $this;
    }
}
