<?php

declare(strict_types=1);

namespace App\Service\Listing\ListingList;

use App\Entity\Category;
use App\Entity\CustomField;
use App\Entity\Listing;
use App\Helper\Search;
use App\Service\Listing\ListingPublicDisplayService;
use App\Service\System\Pagination\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;
use Pagerfanta\Pagerfanta;

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

    public function __construct(
        EntityManagerInterface $em,
        ListingPublicDisplayService $listingPublicDisplayService,
        PaginationService $paginationService
    ) {
        $this->em = $em;
        $this->listingPublicDisplayService = $listingPublicDisplayService;
        $this->paginationService = $paginationService;
    }

    public function getListings(ListingListDto $listingListDto): ListingListDto
    {
        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');

        if (!empty($_GET['form_custom_field'])) {
            $sqlParamId = 0;
            $usedCustomFieldIdList = [];
            foreach ($_GET['form_custom_field'] as $customFieldId => $customFieldFormValueArray) {
                $sqlParamId++;
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
                        $qb->orWhere($rangeCondition);

                        $rangeCondition->add($qb->expr()->eq('listingCustomFieldValue.customField', ':customFieldId_' . ((int) $sqlParamId)));
                        $qb->setParameter(':customFieldId_' . ((int) $sqlParamId), $customFieldId);

                        $usedCustomFieldIdList[] = $customFieldId;
                    }
                }

                if (isset($customFieldFormValueArray['values'])) {
                    foreach ($customFieldFormValueArray['values'] as $valueItem) {
                        $sqlParamId++;
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
                $qb->innerJoin('listing.listingCustomFieldValues', 'listingCustomFieldValue');
                $qb->innerJoin('listingCustomFieldValue.customField', 'customField');

                $qb->andHaving($qb->expr()->eq($qb->expr()->countDistinct('listingCustomFieldValue.id'), ':uniqueCustomFieldsCount'));
                $qb->setParameter(':uniqueCustomFieldsCount', $customFieldsCount);
            }
        }

        if ($listingListDto->getCategory()) {
            $qb->join('listing.category', 'category');
            $qb->andWhere(
                $qb->expr()->andX(
                    $qb->expr()->gte('category.lft', ':requestedCategoryLft'),
                    $qb->expr()->lte('category.rgt', ':requestedCategoryRgt')
                )
            );
            $qb->setParameter(':requestedCategoryLft', $listingListDto->getCategory()->getLft());
            $qb->setParameter(':requestedCategoryRgt', $listingListDto->getCategory()->getRgt());
        }

        if (!empty($_GET['min_price'])) {
            $qb->andWhere($qb->expr()->gte('listing.price', ':minPrice'));
            $qb->setParameter(':minPrice', $_GET['min_price']);
        }

        if (!empty($_GET['max_price'])) {
            $qb->andWhere($qb->expr()->lte('listing.price', ':maxPrice'));
            $qb->setParameter(':maxPrice', $_GET['max_price']);
        }

        if (!empty($_GET['query'])) {
            $qb->andWhere('MATCH (listing.searchText) AGAINST (:query BOOLEAN) > 0');
            $qb->setParameter(':query', Search::optimizeMatch($_GET['query']));
        }

        if (!empty($_GET['user'])) {
            $qb->andWhere($qb->expr()->eq('listing.user', ':user'));
            $qb->setParameter(':user', (int) $_GET['user']);
        }

        $this->listingPublicDisplayService->applyPublicDisplayConditions($qb);

        $qb->addOrderBy('listing.featured', 'DESC');
        $qb->addOrderBy('listing.featuredWeight', 'DESC');
        $qb->addOrderBy('listing.orderByDate', 'DESC');

        if ($listingListDto->isLastAddedListFlag()) {
            $qb->orderBy('listing.orderByDate', 'DESC');
        }

        $qb->groupBy('listing.id');

        $adapter = new DoctrineORMAdapter($qb, true, $qb->getDQLPart('having') !== null);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage($this->paginationService->getMaxPerPage());
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
