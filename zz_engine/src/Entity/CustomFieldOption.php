<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CustomFieldOptionRepository")
 * @UniqueEntity(fields={"value", "customField"})
 */
class CustomFieldOption
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=false)
     */
    private $id;

    /**
     * @var CustomField
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\CustomField", inversedBy="customFieldOptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customField;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $value;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=false, options={"unsigned"=true})
     */
    private $sort;

    public function setValue(?string $value): self
    {
        if (\is_string($value)) {
            $this->value = \mb_strtolower($value);
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdNotNull(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getNameNotNull(): string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getValueNotNull(): string
    {
        return $this->value;
    }

    public function getCustomField(): ?CustomField
    {
        return $this->customField;
    }

    public function getCustomFieldNotNull(): CustomField
    {
        return $this->customField;
    }

    public function setCustomField(?CustomField $customField): self
    {
        $this->customField = $customField;

        return $this;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(?int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }
}
