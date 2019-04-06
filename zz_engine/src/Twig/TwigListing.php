<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Listing;
use Twig\Extension\RuntimeExtensionInterface;

class TwigListing implements RuntimeExtensionInterface
{
    public function listingToActivateForAdmin(Listing $listing): bool
    {
        if ($listing->getAdminRemoved()) {
            return false;
        }

        if ($listing->getUserRemoved()) {
            return false;
        }

        if ($listing->getAdminRejected()) {
            return true;
        }

        if ($listing->getAdminActivated()) {
            return false;
        }

        if ($listing->getUserDeactivated()) {
            return false;
        }

        return true;
    }

    public function listingToRejectForAdmin(Listing $listing): bool
    {
        if ($listing->getAdminRemoved()) {
            return false;
        }

        if ($listing->getUserRemoved()) {
            return false;
        }

        if ($listing->getAdminRejected()) {
            return false;
        }

        return true;
    }
}
