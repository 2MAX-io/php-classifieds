<?php

declare(strict_types=1);

namespace App\Service\Listing\ShowSingle;

use App\Entity\Listing;

class ListingShowDto
{
    /**
     * @var Listing
     */
    private $listing;

    /**
     * @var int
     */
    private $viewsCount;

    public static function fromDoctrineResult(?array $result): ?ListingShowDto
    {
        if (empty($result)) {
            return null;
        }

        $listingShowDto = new static();
        $listingShowDto->setListing($result[0]);
        $listingShowDto->setViewsCount((int) $result['viewsCount']);

        return $listingShowDto;
    }

    public function getListing(): Listing
    {
        return $this->listing;
    }

    public function setListing(Listing $listing): void
    {
        $this->listing = $listing;
    }

    public function getViewsCount(): int
    {
        return $this->viewsCount;
    }

    public function setViewsCount(int $viewsCount): void
    {
        $this->viewsCount = $viewsCount;
    }
}
