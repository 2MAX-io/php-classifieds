<?php

declare(strict_types=1);

namespace App\Service\Listing\CustomField;

use App\Entity\CustomField;
use App\Entity\CustomFieldOption;
use App\Entity\Listing;
use App\Entity\ListingCustomFieldValue;
use App\Helper\Arr;
use App\Helper\Str;
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
        $qb->join('customField.categoriesJoin', 'categoryJoin');
        $qb->join('categoryJoin.category', 'category');
        $qb->leftJoin('customField.customFieldOptions', 'customField_options');

        $qb->andWhere($qb->expr()->eq('category.id', ':category'));
        $qb->setParameter(':category', $categoryId);

        $qb->addSelect('listingCustomFieldValues');
        $qb->leftJoin(
            'customField.listingCustomFieldValues',
            'listingCustomFieldValues',
            Join::WITH,
            $qb->expr()->eq('listingCustomFieldValues.listing', ':listingId')
        );
        $qb->setParameter(':listingId', $listingId);

        $qb->orderBy('categoryJoin.sort', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function saveCustomFieldsToListing(Listing $listing, array $customFieldValueList): void
    {
        $listingCustomFieldValues = $this->getListingCustomFieldValues($listing);
        $listingCustomFieldValuesToRemove = $listingCustomFieldValues;

        /** @var string|array $customFieldValue */
        foreach ($customFieldValueList as $customFieldId => $customFieldValue) {
            if (\is_array($customFieldValue)) {
                foreach ($customFieldValue as $customFieldValueItem) {
                    $listingCustomFieldValue = $this->saveField(
                        $customFieldId,
                        $customFieldValueItem,
                        $listingCustomFieldValues,
                        $listingCustomFieldValuesToRemove,
                        true
                    );
                    $listing->addListingCustomFieldValue($listingCustomFieldValue);
                }
                continue;
            }

            if (Str::emptyTrim($customFieldValue)) {
                continue;
            }

            $listingCustomFieldValue = $this->saveField(
                $customFieldId,
                $customFieldValue,
                $listingCustomFieldValues,
                $listingCustomFieldValuesToRemove
            );
            $listing->addListingCustomFieldValue($listingCustomFieldValue);
        }

        foreach ($listingCustomFieldValuesToRemove as $listingCustomFieldValue) {
            $this->em->remove($listingCustomFieldValue);
        }
    }

    public function saveField(
        $customFieldId,
        $customFieldValue,
        $listingCustomFieldValues,
        &$listingCustomFieldValuesToRemove,
        bool $multiple = false
    ): ListingCustomFieldValue {
        $option = null;
        if (Str::beginsWith($customFieldValue, '__form_custom_field_option_id_')) {
            $optionId = (int) (str_replace('__form_custom_field_option_id_', '', $customFieldValue));
            $option = $this->em->getRepository(CustomFieldOption::class)->find((int) $optionId);
            $customFieldValue = $option->getValue();
        }

        if ($multiple) {
            if (isset($listingCustomFieldValues[$customFieldId . '_' . $customFieldValue])) {
                $listingCustomFieldValue = $listingCustomFieldValues[$customFieldId . '_' . $customFieldValue];
                unset($listingCustomFieldValuesToRemove[$customFieldId . '_' . $customFieldValue]);
            } else {
                $listingCustomFieldValue = new ListingCustomFieldValue();
            }
        } else {
            if (isset($listingCustomFieldValues[$customFieldId])) {
                $listingCustomFieldValue = $listingCustomFieldValues[$customFieldId];
                unset($listingCustomFieldValuesToRemove[$customFieldId]);
            } else {
                $listingCustomFieldValue = new ListingCustomFieldValue();
            }
        }

        $listingCustomFieldValue->setValue($customFieldValue);
        $listingCustomFieldValue->setCustomFieldOption($option);
        $listingCustomFieldValue->setCustomField($this->em->getReference(CustomField::class, $customFieldId));
        $this->em->persist($listingCustomFieldValue);

        return $listingCustomFieldValue;
    }

    /**
     * indexed by custom field unique identifier
     *
     * @return ListingCustomFieldValue[]
     */
    private function getListingCustomFieldValues(Listing $listing): array
    {
        return Arr::indexBy($listing->getListingCustomFieldValues()->toArray(), function(ListingCustomFieldValue $customFieldValue) {
            if ($customFieldValue->getCustomField()->getType() === CustomField::TYPE_CHECKBOX_MULTIPLE) {
                return [$customFieldValue->getCustomField()->getId() . '_' . $customFieldValue->getValue() => $customFieldValue];
            }

            return [$customFieldValue->getCustomField()->getId() => $customFieldValue];
        });
    }
}
