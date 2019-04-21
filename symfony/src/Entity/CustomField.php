<?php

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
     * @ORM\Column(type="smallint")
     */
    private $sortOrder;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $unit;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CustomFieldOption", mappedBy="customField")
     */
    private $customFieldOptions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ListingCustomFieldValue", mappedBy="customField")
     */
    private $listingCustomFieldValues;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="customFields")
     */
    private $categories;

    public function __construct()
    {
        $this->customFieldOptions = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->listingCustomFieldValues = new ArrayCollection();
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

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): self
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
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
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
}
