<?php

declare(strict_types=1);

namespace App\Service\Listing\ListingList;

use App\Entity\Category;
use App\Entity\CustomField;
use App\Entity\Listing;
use App\Helper\Arr;
use App\Helper\Search;
use App\Helper\Str;
use App\Service\Listing\ListingPublicDisplayService;
use App\Service\System\Pagination\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\HttpFoundation\RequestStack;

class ListingListService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ListingPublicDisplayService
     */
    private $listingPublicDisplayService;

    /**
     * @var PaginationService
     */
    private $paginationService;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        EntityManagerInterface $em,
        ListingPublicDisplayService $listingPublicDisplayService,
        RequestStack $requestStack,
        PaginationService $paginationService
    ) {
        $this->em = $em;
        $this->listingPublicDisplayService = $listingPublicDisplayService;
        $this->paginationService = $paginationService;
        $this->requestStack = $requestStack;
    }

    public function getListings(ListingListDto $listingListDto): ListingListDto
    {
        $request = $this->requestStack->getMasterRequest();
        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');

        if ($listingListDto->getCategory()) {
            $qb->join(
                'listing.category',
                'category',
                Join::WITH,
                $qb->expr()->andX(
                    $qb->expr()->gte('category.lft', ':requestedCategoryLft'),
                    $qb->expr()->lte('category.rgt', ':requestedCategoryRgt')
                )
            );
            $qb->setParameter(':requestedCategoryLft', $listingListDto->getCategory()->getLft());
            $qb->setParameter(':requestedCategoryRgt', $listingListDto->getCategory()->getRgt());
        }

        if ($request->get('min_price', false)) {
            $qb->andWhere($qb->expr()->gte('listing.price', ':minPrice'));
            $qb->setParameter(':minPrice', $request->get('min_price', false));
        }

        if ($request->get('max_price', false)) {
            $qb->andWhere($qb->expr()->lte('listing.price', ':maxPrice'));
            $qb->setParameter(':maxPrice', $request->get('max_price', false));
        }

        if ($request->get('query', false)) {
            $qb->andWhere('MATCH (listing.searchText) AGAINST (:query BOOLEAN) > 0');
            $qb->setParameter(':query', Search::optimizeMatch($request->get('query', false)));
        }

        if ($request->get('user', false)) {
            $qb->andWhere($qb->expr()->eq('listing.user', ':user'));
            $qb->setParameter(':user', (int) $request->get('user', false));
        }

        $this->listingPublicDisplayService->applyPublicDisplayConditions($qb);

        if ($request->get('form_custom_field', false)) {
            $customFieldForCategoryList = Arr::indexBy($listingListDto->getCustomFieldForCategoryList(), function(CustomField $customField) {
                return [$customField->getId() => $customField];
            });

            $sqlParamId = 0;
            $usedCustomFieldIdList = [];
            $customFieldConditionList = $qb->expr()->orX();
            foreach ($request->get('form_custom_field') as $customFieldId => $customFieldFormValueArray) {
                $sqlParamId++;
                /** @var CustomField $customField */
                if (!isset($customFieldForCategoryList[$customFieldId])) {
                    continue;
                }
                $customField = $customFieldForCategoryList[$customFieldId];

                if (isset($customFieldFormValueArray['range'])) {
                    $rangeCondition = $qb->expr()->andX();

                    if (!empty($customFieldFormValueArray['range']['min'])) {
                        $rangeCondition->add($qb->expr()->gte('listingCustomFieldValue.value', ':customFieldValueMin_' . ((int) $sqlParamId)));
                        $qb->setParameter(
                            ':customFieldValueMin_' . ((int)$sqlParamId),
                            $customFieldFormValueArray['range']['min'],
                            \Doctrine\DBAL\Types\Type::INTEGER
                        );
                    }

                    if (!empty($customFieldFormValueArray['range']['max'])) {
                        $rangeCondition->add($qb->expr()->lte('listingCustomFieldValue.value', ':customFieldValueMax_' . ((int) $sqlParamId)));
                        $qb->setParameter(
                            ':customFieldValueMax_' . ((int)$sqlParamId),
                            $customFieldFormValueArray['range']['max'],
                            \Doctrine\DBAL\Types\Type::INTEGER
                        );
                    }

                    if ($rangeCondition->count() > 0) {
                        $customFieldConditionList->add($rangeCondition);

                        $rangeCondition->add($qb->expr()->eq('listingCustomFieldValue.customField', ':customFieldId_' . ((int) $sqlParamId)));
                        $qb->setParameter(':customFieldId_' . ((int) $sqlParamId), $customFieldId);

                        $usedCustomFieldIdList[] = $customFieldId;
                    }
                }

                if (isset($customFieldFormValueArray['values'])) {
                    foreach ($customFieldFormValueArray['values'] as $valueItem) {
                        if (Str::emptyTrim($valueItem)) {
                            continue;
                        }

                        $sqlParamId++;
                        $customFieldConditionList->add($qb->expr()->andX(
                            $qb->expr()->eq('listingCustomFieldValue.customField', ':customFieldId_' . ((int) $sqlParamId)),
                            $qb->expr()->eq('listingCustomFieldValue.value', ':customFieldValue_' . ((int) $sqlParamId))
                        ));
                        $qb->setParameter(':customFieldId_' . ((int) $sqlParamId), $customFieldId);
                        $qb->setParameter(':customFieldValue_' . ((int) $sqlParamId), $valueItem);

                        if ($customField->getType() === CustomField::TYPE_CHECKBOX_MULTIPLE) {
                            $usedCustomFieldIdList[] = $customFieldId . "_$valueItem";
                        } else {
                            $usedCustomFieldIdList[] = $customFieldId;
                        }
                    }
                }
            }

            $customFieldsCount = \count(\array_unique($usedCustomFieldIdList));
            if ($customFieldsCount > 0) {
                $qb->join('listing.listingCustomFieldValues', 'listingCustomFieldValue');
                $qb->andWhere($customFieldConditionList);

                $qb->andHaving($qb->expr()->eq($qb->expr()->countDistinct('listingCustomFieldValue.id'), ':uniqueCustomFieldsCount'));
                $qb->setParameter(':uniqueCustomFieldsCount', $customFieldsCount);
            }
        }

        $qb->addOrderBy('listing.featured', 'DESC');
        $qb->addOrderBy('listing.featuredWeight', 'DESC');
        $qb->addOrderBy('listing.orderByDate', 'DESC');

        if ($listingListDto->isLastAddedListFlag()) {
            $qb->orderBy('listing.orderByDate', 'DESC');
        }

        $qb->groupBy('listing.id');

        $pager = $this->paginationService->createPaginationForQb($qb);
        $pager->setAllowOutOfRangePages(true);
        $pager->setCurrentPage($listingListDto->getPageNumber());
        if ($pager->getCurrentPage() > $pager->getNbPages()) {
            $listingListDto->setRedirectToPageNumber($pager->getNbPages());
        }

        $listingListDto->setPager($pager);
        $listingListDto->setResults($pager->getCurrentPageResults());

        return $listingListDto;
    }

    /**
     * @return CustomField[]
     */
    public function getCustomFields(?Category $category): array
    {
        $qb = $this->em->getRepository(CustomField::class)->createQueryBuilder('customField');
        $qb->addSelect('customFieldOption');
        $qb->join('customField.categoriesJoin', 'categoryJoin');
        $qb->join('categoryJoin.category', 'category');
        $qb->leftJoin('customField.customFieldOptions', 'customFieldOption');

        $qb->andWhere($qb->expr()->eq('customField.searchable', 1));
        $qb->andWhere($qb->expr()->eq('category.id', ':category'));
        $qb->setParameter(':category', $category);

        $qb->addOrderBy('categoryJoin.sort', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
