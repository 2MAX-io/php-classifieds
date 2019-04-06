<?php

declare(strict_types=1);

namespace App\Helper;

use App\Entity\Listing;

class ListingFileHelper
{
    public static function getDestinationDirectory(Listing $listing): string
    {
        $userDivider = \floor($listing->getUserNotNull()->getId() / 10000) + 1;

        return FilePath::getListingFilePath()
            .'/'
            .$userDivider
            .'/'
            .'user_'.$listing->getUserNotNull()->getId()
            .'/'
            .'listing_'.$listing->getId();
    }
}
