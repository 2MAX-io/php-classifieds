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
    public const SELECT_AS_CHECKBOXES = 'SELECT_AS_CHECKBOXES';
    public const SELECT_SINGLE = 'SELECT_SINGLE';
    public const CHECKBOX_MULTIPLE = 'CHECKBOX_MULTIPLE';
    public const INTEGER_RANGE = 'INTEGER_RANGE';
    public const YEAR_RANGE = 'YEAR_RANGE';

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
     * @ORM\Column(type="string", length=40, nullable=false)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $nameForAdmin;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=40, nullable=false)
     */
    private $type;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $required;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $searchable;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=false, options={"unsigned"=true})
     */
    private $sort;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $unit;

    /**
     * @var CustomFieldOption[]|Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\CustomFieldOption", mappedBy="customField")
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    private $customFieldOptions;

    /**
     * @var ListingCustomFieldValue[]|Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\ListingCustomFieldValue", mappedBy="customField")
     */
    private $listingCustomFieldValues;

    /**
     * @var CustomFieldJoinCategory[]|Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\CustomFieldJoinCategory", mappedBy="customField")
     */
    private $categoriesJoin;

    public function __construct()
    {
        $this->customFieldOptions = new ArrayCollection();
        $this->listingCustomFieldValues = new ArrayCollection();
        $this->categoriesJoin = new ArrayCollection();
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
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
     * @return Collection|ListingCustomFieldValue[]
     */
    public function getListingCustomFieldValuesArray(): array
    {
        return $this->getListingCustomFieldValues()->map(static function (ListingCustomFieldValue $value) {
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
