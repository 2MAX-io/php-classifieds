<?php

declare(strict_types=1);

namespace App\Service\Listing\ListingList;

use App\Entity\Category;
use App\Entity\CustomField;
use App\Entity\Listing;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;

class ListingListService
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
     * @return Listing[]
     */
    public function getListings(Category $category = null): array
    {
        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');
        $qb->leftJoin('listing.listingCustomFieldValues', 'listingCustomFieldValue');
        $qb->leftJoin('listingCustomFieldValue.customField', 'customField');
        $qb->leftJoin('listing.category', 'category');

        if (!empty($_GET['form_custom_field'])) {
            $sqlParamId = 0;
            $usedCustomFieldIdList = [];
            foreach ($_GET['form_custom_field'] as $customFieldId => $customFieldFormValueArray) {
                $sqlParamId++;
                if (isset($customFieldFormValueArray['range'])) {
                    $qb->orWhere($qb->expr()->andX(
                        $qb->expr()->eq('listingCustomFieldValue.customField', ':customFieldId_' . ((int) $sqlParamId)),
                        $qb->expr()->gte('listingCustomFieldValue.value', ':customFieldValueMin_' . ((int) $sqlParamId)),
                        $qb->expr()->lte('listingCustomFieldValue.value', ':customFieldValueMax_' . ((int) $sqlParamId))
                    ));
                    $qb->setParameter(':customFieldId_' . ((int) $sqlParamId), $customFieldId);
                    $qb->setParameter(':customFieldValueMin_' . ((int) $sqlParamId), $customFieldFormValueArray['range']['min']);
                    $qb->setParameter(':customFieldValueMax_' . ((int) $sqlParamId), $customFieldFormValueArray['range']['max']);

                    $usedCustomFieldIdList[] = $customFieldId;
                }

                if (isset($customFieldFormValueArray['values'])) {
                    foreach ($customFieldFormValueArray as $valueItem) {
                        $qb->orWhere($qb->expr()->andX(
                            $qb->expr()->eq('listingCustomFieldValue.customField', ':customFieldId_' . ((int) $sqlParamId)),
                            $qb->expr()->eq('listingCustomFieldValue.value', ':customFieldValue_' . ((int) $sqlParamId))
                        ));
                        $qb->setParameter(':customFieldId_' . ((int) $sqlParamId), $customFieldId);
                        $qb->setParameter(':customFieldValue_' . ((int) $sqlParamId), $valueItem);

                        $usedCustomFieldIdList[] = $customFieldId;
                    }
                }
            }

            $qb->andHaving($qb->expr()->eq($qb->expr()->countDistinct('listingCustomFieldValue.id'), ':uniqueCustomFieldsCount'));
            $qb->setParameter(':uniqueCustomFieldsCount', count(array_unique($usedCustomFieldIdList)));

            $qb->groupBy('listing.id');
        }

        if ($category) {
            $qb->leftJoin(
                Category::class,
                'requestedCategory',
                Join::WITH,
                $qb->expr()->andX($qb->expr()->eq('requestedCategory.id', ':requestedCategory'))
            );
            $qb->setParameter(':requestedCategory', $category);

            $qb->andWhere(
                $qb->expr()->andX(
                    $qb->expr()->gte('category.lft', 'requestedCategory.lft'),
                    $qb->expr()->lte('category.rgt', 'requestedCategory.rgt')
                )
            );
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return CustomField[]
     */
    public function getCustomFields(): array
    {
        $qb = $this->em->getRepository(CustomField::class)->createQueryBuilder('custom_field');
        $qb->leftJoin('custom_field.customFieldOptions', 'custom_field_options');

        return $qb->getQuery()->getResult();
    }
}
