<?php

declare(strict_types=1);

namespace App\Service\Listing\Save;

use App\Entity\Listing;
use DateTime;

class CreateListingService
{
    public function create(): Listing
    {
        $listing = new Listing();
        $listing->setFirstCreatedDate(new DateTime());

        return $listing;
    }
}
