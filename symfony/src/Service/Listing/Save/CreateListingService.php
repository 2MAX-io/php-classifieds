<?php

declare(strict_types=1);

namespace App\Service\Listing\Save;

use App\Entity\Listing;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use DateTime;
use Symfony\Component\Form\FormInterface;

class CreateListingService
{
    public function create(): Listing
    {
        $listing = new Listing();
        $listing->setFirstCreatedDate(new DateTime());
        $listing->setLastEditDate(new DateTime());
        $listing->setLastReactivationDate(new DateTime());

        return $listing;
    }

    public function setFormDependent(Listing $listing, FormInterface $form): void
    {
        $validityTimeDays = (int) $form->get('validityTimeDays')->getData();

        $listing->setValidUntilDate(Carbon::now()->add(CarbonInterval::days($validityTimeDays)));
    }
}
