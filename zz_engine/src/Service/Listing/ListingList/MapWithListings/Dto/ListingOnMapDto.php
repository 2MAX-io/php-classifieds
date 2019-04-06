<?php

declare(strict_types=1);

namespace App\Service\Listing\ListingList\MapWithListings\Dto;

use App\Entity\Listing;
use App\Enum\ParamEnum;
use App\Helper\ResizedImagePath;

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
     * @var string|null
     */
    private $listingMainImage;

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
        $listingOnMapDto = new self();
        $listingOnMapDto->setListingId($listing->getId());
        $listingOnMapDto->setListingSlug($listing->getSlug());
        $listingOnMapDto->setListingTitle($listing->getTitle());
        $listingOnMapDto->setLatitude($listing->getLocationLatitude());
        $listingOnMapDto->setLongitude($listing->getLocationLongitude());
        $listingOnMapDto->setListingMainImage($listing->getMainImage(ResizedImagePath::LIST));

        return $listingOnMapDto;
    }

    /**
     * @return array<string,float|int|string|null>
     */
    public function jsonSerialize(): array
    {
        return [
            ParamEnum::LISTING_ID => $this->getListingId(),
            'listingSlug' => $this->getListingSlug(),
            'listingTitle' => $this->getListingTitle(),
            'mainImage' => $this->getListingMainImage(),
            ParamEnum::LATITUDE => $this->getLatitude(),
            ParamEnum::LONGITUDE => $this->getLongitude(),
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

    public function getListingMainImage(): ?string
    {
        return $this->listingMainImage;
    }

    public function setListingMainImage(?string $listingMainImage): void
    {
        $this->listingMainImage = $listingMainImage;
    }
}
