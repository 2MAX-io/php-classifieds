<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CustomFieldRepository")
 */
class CustomField
{
    public const TYPE_SELECT = 'select';
    public const TYPE_INTEGER_RANGE = 'integer_range';
    public const TYPE_YEAR_RANGE = 'year_range';
    public const TYPE_CHECKBOX_MULTIPLE = 'checkbox_multiple';
    public const TYPE_SELECT_SINGLE = 'select_single';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $nameForAdmin;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $type;

    /**
     * @ORM\Column(type="boolean")
     */
    private $required;

    /**
     * @ORM\Column(type="boolean")
     */
    private $searchable;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $unit;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CustomFieldOption", mappedBy="customField")
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    private $customFieldOptions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ListingCustomFieldValue", mappedBy="customField")
     */
    private $listingCustomFieldValues;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CustomFieldJoinCategory", mappedBy="customField")
     */
    private $categoriesJoin;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=false, options={"unsigned"=true})
     */
    private $sort;

    public function __construct()
    {
        $this->customFieldOptions = new ArrayCollection();
        $this->listingCustomFieldValues = new ArrayCollection();
        $this->categoriesJoin = new ArrayCollection();
    }

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getRequired(): ?bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): self
    {
        $this->required = $required;

        return $this;
    }

    public function getSearchable(): ?bool
    {
        return $this->searchable;
    }

    public function setSearchable(bool $searchable): self
    {
        $this->searchable = $searchable;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * @return Collection|CustomFieldOption[]
     */
    public function getCustomFieldOptions(): Collection
    {
        return $this->customFieldOptions;
    }

    public function addCustomFieldOption(CustomFieldOption $customFieldOption): self
    {
        if (!$this->customFieldOptions->contains($customFieldOption)) {
            $this->customFieldOptions[] = $customFieldOption;
            $customFieldOption->setCustomField($this);
        }

        return $this;
    }

    public function removeCustomFieldOption(CustomFieldOption $customFieldOption): self
    {
        if ($this->customFieldOptions->contains($customFieldOption)) {
            $this->customFieldOptions->removeElement($customFieldOption);
            // set the owning side to null (unless already changed)
            if ($customFieldOption->getCustomField() === $this) {
                $customFieldOption->setCustomField(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ListingCustomFieldValue[]
     */
    public function getListingCustomFieldValues(): Collection
    {
        return $this->listingCustomFieldValues;
    }

    /**
     * @return ListingCustomFieldValue|null
     */
    public function getListingCustomFieldValueFirst(): ?ListingCustomFieldValue
    {
        if ($this->getListingCustomFieldValues()->first()) {
            return $this->getListingCustomFieldValues()->first();
        }

        return null;
    }

    /**
     * @return Collection|ListingCustomFieldValue[]
     */
    public function getListingCustomFieldValuesArray(): array
    {
        return $this->getListingCustomFieldValues()->map(function (ListingCustomFieldValue $value) {
            return $value->getValue();
        })->toArray();
    }

    public function addListingCustomFieldValue(ListingCustomFieldValue $listingCustomFieldValue): self
    {
        if (!$this->listingCustomFieldValues->contains($listingCustomFieldValue)) {
            $this->listingCustomFieldValues[] = $listingCustomFieldValue;
            $listingCustomFieldValue->setCustomField($this);
        }

        return $this;
    }

    public function removeListingCustomFieldValue(ListingCustomFieldValue $listingCustomFieldValue): self
    {
        if ($this->listingCustomFieldValues->contains($listingCustomFieldValue)) {
            $this->listingCustomFieldValues->removeElement($listingCustomFieldValue);
            // set the owning side to null (unless already changed)
            if ($listingCustomFieldValue->getCustomField() === $this) {
                $listingCustomFieldValue->setCustomField(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CustomFieldJoinCategory[]
     */
    public function getCategoriesJoin(): Collection
    {
        return $this->categoriesJoin;
    }

    public function addCategoriesJoin(CustomFieldJoinCategory $categoriesJoin): self
    {
        if (!$this->categoriesJoin->contains($categoriesJoin)) {
            $this->categoriesJoin[] = $categoriesJoin;
            $categoriesJoin->setCustomField($this);
        }

        return $this;
    }

    public function removeCategoriesJoin(CustomFieldJoinCategory $categoriesJoin): self
    {
        if ($this->categoriesJoin->contains($categoriesJoin)) {
            $this->categoriesJoin->removeElement($categoriesJoin);
            // set the owning side to null (unless already changed)
            if ($categoriesJoin->getCustomField() === $this) {
                $categoriesJoin->setCustomField(null);
            }
        }

        return $this;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function getNameForAdmin(): ?string
    {
        return $this->nameForAdmin;
    }

    public function setNameForAdmin(?string $nameForAdmin): self
    {
        $this->nameForAdmin = $nameForAdmin;

        return $this;
    }
}
