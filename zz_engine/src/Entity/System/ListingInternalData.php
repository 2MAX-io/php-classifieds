<?php

declare(strict_types=1);

namespace App\Entity\System;

use App\Entity\Listing;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="zzzz_listing_internal_data")
 */
class ListingInternalData
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Listing", inversedBy="listingInternalData")
     * @ORM\JoinColumn(nullable=false, unique=true)
     */
    private $listing;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastListingRegenerationDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastListingRegenerationDate(): ?\DateTimeInterface
    {
        return $this->lastListingRegenerationDate;
    }

    public function setLastListingRegenerationDate(?\DateTimeInterface $lastListingRegenerationDate): self
    {
        $this->lastListingRegenerationDate = $lastListingRegenerationDate;

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
}
