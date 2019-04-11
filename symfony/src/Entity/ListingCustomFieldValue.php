<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ListingCustomFieldValueRepository")
 */
class ListingCustomFieldValue
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Listing", inversedBy="listingCustomFieldValues")
     */
    private $listing;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CustomField", inversedBy="listingCustomFieldValues")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customField;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CustomFieldOption")
     * @ORM\JoinColumn(nullable=true)
     */
    private $customFieldOption;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $value;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getListing(): ?Listing
    {
        return $this->listing;
    }

    public function setListing(?Listing $listing): self
    {
        $this->listing = $listing;

        return $this;
    }

    public function getCustomField(): ?CustomField
    {
        return $this->customField;
    }

    public function setCustomField(?CustomField $customField): self
    {
        $this->customField = $customField;

        return $this;
    }

    public function getCustomFieldOption(): ?CustomFieldOption
    {
        return $this->customFieldOption;
    }

    public function setCustomFieldOption(?CustomFieldOption $customFieldOption): self
    {
        $this->customFieldOption = $customFieldOption;

        return $this;
    }
}
