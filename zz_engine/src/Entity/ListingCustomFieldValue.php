<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *      uniqueConstraints={
 *         @UniqueConstraint(columns={"listing_id", "custom_field_id", "value", "custom_field_option_id"}, name="unique_custom_field_value_in_listing"),
 *      },
 *      indexes={
 *          @Index(columns={"listing_id", "custom_field_id", "value"}, name="IDX_filter"),
 *      },
 * )
 */
class ListingCustomFieldValue
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
     * @var Listing
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Listing", inversedBy="listingCustomFieldValues")
     * @ORM\JoinColumn(nullable=false)
     */
    private $listing;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=70, nullable=false)
     */
    private $value;

    /**
     * @var CustomField
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\CustomField", inversedBy="listingCustomFieldValues")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customField;

    /**
     * @var null|CustomFieldOption
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\CustomFieldOption")
     * @ORM\JoinColumn(nullable=true)
     */
    private $customFieldOption;

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

    public function getCustomFieldNotNull(): CustomField
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
