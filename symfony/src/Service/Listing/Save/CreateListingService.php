<?php

declare(strict_types=1);

namespace App\Service\Listing\Save;

use App\Entity\Listing;
use App\Service\Listing\ValidityExtend\ValidUntilSetService;
use DateTime;
use Symfony\Component\Form\FormInterface;

class CreateListingService
{
    /**
     * @var ValidUntilSetService
     */
    private $validUntilSetService;

    public function __construct(ValidUntilSetService $validUntilSetService)
    {
        $this->validUntilSetService = $validUntilSetService;
    }

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
        if ($form->has('validityTimeDays')) {
            $this->validUntilSetService->setValidUntil($listing, (int) $form->get('validityTimeDays')->getData());
        }

        $listing->loadSearchText();
    }
}
