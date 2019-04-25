<?php

namespace App\Entity;

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
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     */
    private $tokenString;

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

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TokenField", mappedBy="token", indexBy="name", cascade={"all"})
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
        /** @var TokenField $field */
        $field = $this->getFields()->get($fieldName);

        if ($field === null) {
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
}
