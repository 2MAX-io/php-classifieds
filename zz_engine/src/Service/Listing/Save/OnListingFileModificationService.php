<?php

declare(strict_types=1);

namespace App\Service\Listing\Save;

use App\Entity\Listing;
use App\Entity\ListingFile;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ExpressionBuilder;
use Doctrine\ORM\EntityManagerInterface;

class OnListingFileModificationService
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
        $expr = Criteria::expr();
        if (!$expr instanceof ExpressionBuilder) {
            throw new \RuntimeException('Criteria::expr() returns null');
        }

        /** @var bool|ListingFile $firstFile */
        $firstFile = $listing->getListingFiles()->matching(
            Criteria::create()
                ->orderBy(['sort' => Criteria::ASC])
                ->where($expr->eq('userRemoved', false))
        )->first();

        if ($firstFile instanceof ListingFile) {
            $listing->setMainImage($firstFile->getPath());
        } else {
            $listing->setMainImage(null);
        }

        $this->em->persist($listing);
    }
}
