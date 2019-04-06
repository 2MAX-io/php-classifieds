<?php

declare(strict_types=1);

namespace App\Service\Listing\CustomField;

use App\Entity\CustomField;
use App\Entity\Listing;
use App\Entity\ListingCustomFieldValue;
use Doctrine\ORM\EntityManagerInterface;

class CustomFieldsForListingFormService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return CustomField[]
     */
    public function getFields(): array
    {
        $qb = $this->em->getRepository(CustomField::class)->createQueryBuilder('custom_field');
        $qb->leftJoin('custom_field.customFieldOptions', 'custom_field_options');
        $qb->orderBy('custom_field.sortOrder');

        return $qb->getQuery()->getResult();
    }

    public function saveCustomFieldsToListing(Listing $listing, array $customFieldValueList): void
    {
        foreach ($customFieldValueList as $customFieldId => $customFieldValue) {
            $listingCustomFieldValue = new ListingCustomFieldValue();
            $listingCustomFieldValue->setValue($customFieldValue);
            $listingCustomFieldValue->setCustomField($this->em->getReference(CustomField::class, $customFieldId));
            $this->em->persist($listingCustomFieldValue);
            $listing->addListingCustomFieldValue($listingCustomFieldValue);
        }
    }
}
