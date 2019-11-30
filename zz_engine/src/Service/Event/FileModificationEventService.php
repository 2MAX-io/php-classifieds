<?php

declare(strict_types=1);

namespace App\Service\Event;

use App\Entity\Listing;
use App\Entity\ListingFile;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;

class FileModificationEventService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onFileModificationByListing(Listing $listing): void
    {
        $this->updateListingMainImage($listing);
    }

    public function onFileModification(ListingFile $listingFile): void
    {
        $this->updateListingMainImage($listingFile->getListing());
    }

    public function updateListingMainImage(Listing $listing): void
    {
        /** @var ListingFile $firstFile */
        $firstFile = $listing->getListingFiles()->matching(
            Criteria::create()
                ->orderBy(['sort' => 'asc'])
                ->where(Criteria::expr()->eq('userRemoved', false))
        )->first();

        if ($firstFile instanceof ListingFile) {
            $listing->setMainImage($firstFile->getPath());
        } else {
            $listing->setMainImage(null);
        }

        $this->em->persist($listing);
    }
}
