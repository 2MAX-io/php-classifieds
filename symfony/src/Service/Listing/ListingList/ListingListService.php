<?php

declare(strict_types=1);

namespace App\Service\Listing\ListingList;

use App\Entity\Category;
use App\Entity\CustomField;
use App\Entity\Listing;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

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

    public function getListings(int $page = 1, Category $category = null): ListingListDto
    {
        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');
        $qb->leftJoin('listing.listingCustomFieldValues', 'listingCustomFieldValue');
        $qb->leftJoin('listingCustomFieldValue.customField', 'customField');
        $qb->leftJoin('listing.category', 'category');
        $qb->leftJoin('listing.listingFiles', 'listingFile');

        if (!empty($_GET['form_custom_field'])) {
            $sqlParamId = 0;
            $usedCustomFieldIdList = [];
            foreach ($_GET['form_custom_field'] as $customFieldId => $customFieldFormValueArray) {
                $sqlParamId++;
                if (isset($customFieldFormValueArray['range'])) {
                    $rangeCondition = $qb->expr()->andX();

                    if (!empty($customFieldFormValueArray['range']['min'])) {
                        $rangeCondition->add($qb->expr()->gte('listingCustomFieldValue.value', ':customFieldValueMin_' . ((int) $sqlParamId)));
                        $qb->setParameter(':customFieldValueMin_' . ((int) $sqlParamId), $customFieldFormValueArray['range']['min']);
                    }

                    if (!empty($customFieldFormValueArray['range']['max'])) {
                        $rangeCondition->add($qb->expr()->lte('listingCustomFieldValue.value', ':customFieldValueMax_' . ((int) $sqlParamId)));
                        $qb->setParameter(':customFieldValueMax_' . ((int) $sqlParamId), $customFieldFormValueArray['range']['max']);
                    }

                    if ($rangeCondition->count() > 0) {
                        $qb->orWhere($rangeCondition);

                        $rangeCondition->add($qb->expr()->eq('listingCustomFieldValue.customField', ':customFieldId_' . ((int) $sqlParamId)));
                        $qb->setParameter(':customFieldId_' . ((int) $sqlParamId), $customFieldId);

                        $usedCustomFieldIdList[] = $customFieldId;
                    }
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

            $customFieldsCount = count(array_unique($usedCustomFieldIdList));
            if ($customFieldsCount > 0) {
                $qb->andHaving($qb->expr()->eq($qb->expr()->countDistinct('listingCustomFieldValue.id'), ':uniqueCustomFieldsCount'));
                $qb->setParameter(':uniqueCustomFieldsCount', $customFieldsCount);
            }

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


        $adapter = new DoctrineORMAdapter($qb);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage(20);
        $pager->setCurrentPage($page);

        $qb->setMaxResults($pager->getMaxPerPage());
        $qb->setFirstResult($pager->getCurrentPageOffsetStart());

        return new ListingListDto($pager->getCurrentPageResults(), $pager);
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
