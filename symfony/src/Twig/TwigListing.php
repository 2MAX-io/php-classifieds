<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Listing;
use Twig\Extension\RuntimeExtensionInterface;

class TwigListing implements RuntimeExtensionInterface
{
    public function adminShowActivate(Listing $listing): bool
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

        if ($listing->getAdminConfirmed()) {
            return false;
        }

        if ($listing->getUserDeactivated()) {
            return false;
        }

        return true;
    }

    public function adminShowReject(Listing $listing): bool
    {
        if ($listing->getAdminRemoved()) {
            return false;
        }

        if ($listing->getUserRemoved()) {
            return false;
        }

        return true;
    }
}
