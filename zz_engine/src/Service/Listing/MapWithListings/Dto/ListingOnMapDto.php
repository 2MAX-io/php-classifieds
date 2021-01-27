<?php

declare(strict_types=1);

namespace App\Service\Listing\MapWithListings\Dto;

use App\Entity\Listing;

class ListingOnMapDto implements \JsonSerializable
{
    /**
     * @var int
     */
    private $listingId;

    /**
     * @var string
     */
    private $listingSlug;

    /**
     * @var string
     */
    private $listingTitle;

    /**
     * @var float
     */
    private $latitude;

    /**
     * @var float
     */
    private $longitude;

    public static function fromListing(Listing $listing): self
    {
        $listingOnMapDto = new static();
        $listingOnMapDto->setListingId($listing->getId());
        $listingOnMapDto->setListingSlug($listing->getSlug());
        $listingOnMapDto->setListingTitle($listing->getTitle());
        $listingOnMapDto->setLatitude($listing->getLocationLatitude());
        $listingOnMapDto->setLongitude($listing->getLocationLongitude());

        return $listingOnMapDto;
    }

    public function jsonSerialize(): array
    {
        return [
            'listingId' => $this->getListingId(),
            'listingSlug' => $this->getListingSlug(),
            'listingTitle' => $this->getListingTitle(),
            'latitude' => $this->getLatitude(),
            'longitude' => $this->getLongitude(),
        ];
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function getListingId(): int
    {
        return $this->listingId;
    }

    public function setListingId(int $listingId): void
    {
        $this->listingId = $listingId;
    }

    public function getListingSlug(): string
    {
        return $this->listingSlug;
    }

    public function setListingSlug(string $listingSlug): void
    {
        $this->listingSlug = $listingSlug;
    }

    public function getListingTitle(): string
    {
        return $this->listingTitle;
    }

    public function setListingTitle(string $listingTitle): void
    {
        $this->listingTitle = $listingTitle;
    }
}
