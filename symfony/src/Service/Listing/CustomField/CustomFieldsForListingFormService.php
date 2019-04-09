<?php

declare(strict_types=1);

namespace App\Service\Listing\CustomField;

use App\Entity\CustomField;
use App\Entity\Listing;
use App\Entity\ListingCustomFieldValue;
use App\Security\CurrentUserService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;

class CustomFieldsForListingFormService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    public function __construct(EntityManagerInterface $em, CurrentUserService $currentUserService)
    {
        $this->em = $em;
        $this->currentUserService = $currentUserService;
    }

    /**
     * @return CustomField[]
     */
    public function getFields(?int $categoryId, ?int $listingId): array
    {
        $qb = $this->em->getRepository(CustomField::class)->createQueryBuilder('custom_field');
        $qb->leftJoin('custom_field.customFieldOptions', 'custom_field_options');

        if ($categoryId) {
        }

        if ($listingId) {
            $qb->leftJoin(
                'custom_field.listingCustomFieldValues',
                'listingCustomFieldValues',
                Join::WITH,
                $qb->expr()->eq('listingCustomFieldValues.listing', ':listingId')
            );
            $qb->setParameter(':listingId', $listingId);
        }

        $qb->orderBy('custom_field.sortOrder');

        return $qb->getQuery()->getResult();
    }

    public function saveCustomFieldsToListing(Listing $listing, array $customFieldValueList): void
    {
        foreach ($listing->getListingCustomFieldValues() as $listingCustomFieldValue) {
            $this->em->remove($listingCustomFieldValue);
        }

        foreach ($customFieldValueList as $customFieldId => $customFieldValue) {
            $listingCustomFieldValue = new ListingCustomFieldValue();
            $listingCustomFieldValue->setValue($customFieldValue);
            $listingCustomFieldValue->setCustomField($this->em->getReference(CustomField::class, $customFieldId));
            $this->em->persist($listingCustomFieldValue);
            $listing->addListingCustomFieldValue($listingCustomFieldValue);
        }
    }
}
