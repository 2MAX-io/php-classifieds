<?php

declare(strict_types=1);

namespace App\Service\Listing\CustomField;

use App\Entity\CustomField;
use App\Entity\CustomFieldOption;
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
        $qb = $this->em->getRepository(CustomField::class)->createQueryBuilder('customField');
        $qb->leftJoin('customField.customFieldOptions', 'customField_options');

        if ($categoryId) {
        }

        $qb->addSelect('listingCustomFieldValues');
        $qb->leftJoin(
            'customField.listingCustomFieldValues',
            'listingCustomFieldValues',
            Join::WITH,
            $qb->expr()->eq('listingCustomFieldValues.listing', ':listingId')
        );
        $qb->setParameter(':listingId', $listingId);

        $qb->orderBy('customField.sortOrder');

        return $qb->getQuery()->getResult();
    }

    public function saveCustomFieldsToListing(Listing $listing, array $customFieldValueList): void
    {
        foreach ($listing->getListingCustomFieldValues() as $listingCustomFieldValue) {
            $this->em->remove($listingCustomFieldValue);
        }

        foreach ($customFieldValueList as $customFieldId => $customFieldValue) {
            $option = null;
            if (strpos($customFieldValue, '__form_custom_field_option_id_') === 0) {
                $optionId = (int) (str_replace('__form_custom_field_option_id_', '', $customFieldValue));
                $option = $this->em->getRepository(CustomFieldOption::class)->find((int) $optionId);
                $customFieldValue = $option->getValue();
            }

            $listingCustomFieldValue = new ListingCustomFieldValue();
            $listingCustomFieldValue->setValue($customFieldValue);
            $listingCustomFieldValue->setCustomFieldOption($option);
            $listingCustomFieldValue->setCustomField($this->em->getReference(CustomField::class, $customFieldId));
            $this->em->persist($listingCustomFieldValue);
            $listing->addListingCustomFieldValue($listingCustomFieldValue);
        }
    }
}
