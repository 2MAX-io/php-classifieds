<?php

declare(strict_types=1);

namespace App\Service\Listing\ListingList;

use App\Entity\CustomField;
use App\Entity\Listing;
use App\Enum\ParamEnum;
use App\Helper\ArrayHelper;
use App\Helper\BoolHelper;
use App\Helper\SearchHelper;
use App\Helper\StringHelper;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Security\CurrentUserService;
use App\Service\Listing\ListingList\Dto\ListingListDto;
use App\Service\Listing\ListingList\MapWithListings\Dto\ListingOnMapDto;
use App\Service\Listing\ListingPublicDisplayService;
use App\Service\Listing\Secondary\SaveSearchHistoryService;
use App\Service\System\Pagination\PaginationService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ListingListService
{
    /**
     * @var ListingPublicDisplayService
     */
    private $listingPublicDisplayService;

    /**
     * @var SaveSearchHistoryService
     */
    private $saveSearchHistory;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    /**
     * @var PaginationService
     */
    private $paginationService;

    /**
     * @var EntityManager|EntityManagerInterface
     */
    private $em;

    public function __construct(
        ListingPublicDisplayService $listingPublicDisplayService,
        SaveSearchHistoryService $saveSearchHistory,
        CategoryRepository $categoryRepository,
        UserRepository $userRepository,
        CurrentUserService $currentUserService,
        PaginationService $paginationService,
        EntityManagerInterface $em
    ) {
        $this->listingPublicDisplayService = $listingPublicDisplayService;
        $this->saveSearchHistory = $saveSearchHistory;
        $this->categoryRepository = $categoryRepository;
        $this->userRepository = $userRepository;
        $this->currentUserService = $currentUserService;
        $this->paginationService = $paginationService;
        $this->em = $em;
    }

    public function getListings(ListingListDto $listingListDto): ListingListDto
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('listing');
        $qb->from(Listing::class, 'listing');

        if ($listingListDto->getCategory()) {
            $qb->join(
                'listing.category',
                'category',
                Join::WITH,
                (string) $qb->expr()->andX(
                    $qb->expr()->gte('category.lft', ':requestedCategoryLft'),
                    $qb->expr()->lte('category.rgt', ':requestedCategoryRgt')
                )
            );
            $qb->setParameter(':requestedCategoryLft', $listingListDto->getCategory()->getLft());
            $qb->setParameter(':requestedCategoryRgt', $listingListDto->getCategory()->getRgt());
        }

        if ($listingListDto->getMinPrice()) {
            $qb->andWhere($qb->expr()->gte('listing.price', ':minPrice'));
            $qb->setParameter(':minPrice', $listingListDto->getMinPrice());
        }

        if ($listingListDto->getMaxPrice()) {
            $qb->andWhere($qb->expr()->lte('listing.price', ':maxPrice'));
            $qb->setParameter(':maxPrice', $listingListDto->getMaxPrice());
        }

        if ($listingListDto->getSearchQuery()) {
            $qb->andWhere('MATCH (listing.searchText) AGAINST (:query BOOLEAN) > 0');
            $qb->setParameter(':query', SearchHelper::optimizeMatch($listingListDto->getSearchQuery()));
        }

        if ($listingListDto->getFilterByUser()) {
            $qb->andWhere($qb->expr()->eq('listing.user', ':user'));
            $qb->setParameter(':user', $listingListDto->getFilterByUser()->getId());
        }

        $this->listingPublicDisplayService->applyPublicDisplayConditions($qb);

        if ($listingListDto->getFilterByCustomFields()) {
            /** @var CustomField[] $customFieldListIndexedById */
            $customFieldListIndexedById = ArrayHelper::indexBy(
                $listingListDto->getCategoryCustomFields(),
                static function (CustomField $customField) {
                    return [$customField->getIdNotNull() => $customField];
                }
            );

            $sqlParamNumber = 0;
            $usedCustomFieldFilters = [];
            $customFieldFiltersQueryOrX = $qb->expr()->orX();
            foreach ($listingListDto->getFilterByCustomFields() as $customFieldId => $customFieldFilter) {
                ++$sqlParamNumber;
                $customField = $customFieldListIndexedById[$customFieldId] ?? null;
                if (!$customField) {
                    continue;
                }

                $isRangeFilter = isset($customFieldFilter['range']);
                if ($isRangeFilter) {
                    $rangeCondition = $qb->expr()->andX();
                    if (!empty($customFieldFilter['range']['min'])) {
                        $rangeCondition->add($qb->expr()->gte('listingCustomFieldValue.value', ':customFieldValueMin_'.$sqlParamNumber));
                        $qb->setParameter(
                            ':customFieldValueMin_'.$sqlParamNumber,
                            $customFieldFilter['range']['min'],
                            Types::INTEGER,
                        );
                    }

                    if (!empty($customFieldFilter['range']['max'])) {
                        $rangeCondition->add($qb->expr()->lte('listingCustomFieldValue.value', ':customFieldValueMax_'.$sqlParamNumber));
                        $qb->setParameter(
                            ':customFieldValueMax_'.$sqlParamNumber,
                            $customFieldFilter['range']['max'],
                            Types::INTEGER,
                        );
                    }

                    if ($rangeCondition->count() > 0) {
                        $customFieldFiltersQueryOrX->add($rangeCondition);

                        $rangeCondition->add($qb->expr()->eq('listingCustomFieldValue.customField', ':customFieldId_'.$sqlParamNumber));
                        $qb->setParameter(':customFieldId_'.$sqlParamNumber, $customFieldId);

                        $usedCustomFieldFilters[] = $customFieldId;
                    }
                }

                $isMultipleValuesFilter = isset($customFieldFilter['values']);
                if ($isMultipleValuesFilter) {
                    foreach ($customFieldFilter['values'] as $customFieldValue) {
                        if (StringHelper::emptyTrim($customFieldValue)) {
                            continue;
                        }

                        ++$sqlParamNumber;
                        $customFieldFiltersQueryOrX->add($qb->expr()->andX(
                            $qb->expr()->eq('listingCustomFieldValue.value', ':customFieldValue_'.$sqlParamNumber),
                            $qb->expr()->eq('listingCustomFieldValue.customField', ':customFieldId_'.$sqlParamNumber),
                        ));
                        $qb->setParameter(':customFieldValue_'.$sqlParamNumber, $customFieldValue);
                        $qb->setParameter(':customFieldId_'.$sqlParamNumber, $customFieldId);

                        if (CustomField::CHECKBOX_MULTIPLE === $customField->getType()) {
                            $usedCustomFieldFilters[] = $customFieldId."_{$customFieldValue}";
                        } else {
                            $usedCustomFieldFilters[] = $customFieldId;
                        }
                    }
                }
            }

            $usedCustomFieldFiltersCount = \count(\array_unique($usedCustomFieldFilters));
            if ($usedCustomFieldFiltersCount > 0) {
                $qb->join('listing.listingCustomFieldValues', 'listingCustomFieldValue');
                $qb->andWhere($customFieldFiltersQueryOrX);

                $qb->andHaving($qb->expr()->eq(
                    $qb->expr()->countDistinct('listingCustomFieldValue.id'),
                    ':usedCustomFieldFiltersCount'
                ));
                $qb->setParameter(':usedCustomFieldFiltersCount', $usedCustomFieldFiltersCount);
            }
        }

        $currentUser = $this->currentUserService->getUserOrNull();
        $qb->addSelect('userObservedListing');
        $qb->leftJoin('listing.userObservedListings',
            'userObservedListing',
            Join::WITH,
            (string) $qb->expr()->eq('userObservedListing.user', ':currentUserId'),
        );
        $qb->setParameter(':currentUserId', $currentUser ? $currentUser->getId() : 0);

        if ($listingListDto->getFilterByUserObservedListings()) {
            $qb->andWhere($qb->expr()->isNotNull('userObservedListing.id'));
        }

        if ($listingListDto->getShowOnMap()) {
            $qb->andWhere($qb->expr()->isNotNull('listing.locationLatitude'));
            $qb->andWhere($qb->expr()->isNotNull('listing.locationLongitude'));
            $listingListDto->setMaxResults(3600);
            $listingListDto->setPaginationEnabled(false);
        }

        $qb->addOrderBy('listing.featured', Criteria::DESC);
        $qb->addOrderBy('listing.featuredWeight', Criteria::DESC);
        $qb->addOrderBy('listing.orderByDate', Criteria::DESC);
        $qb->addOrderBy('listing.id', Criteria::DESC);

        if ($listingListDto->isLastAddedList()) {
            $qb->orderBy('listing.firstCreatedDate', Criteria::DESC);
            $qb->addOrderBy('listing.id', Criteria::DESC);
        }

        if ($listingListDto->getFilterByUserObservedListings()) {
            $qb->orderBy('listing.firstCreatedDate', Criteria::DESC);
            $qb->addOrderBy('listing.id', Criteria::DESC);
        }

        $qb->groupBy('listing.id');

        if ($listingListDto->getMaxResults()) {
            $qb->setMaxResults($listingListDto->getMaxResults());
        }

        if ($listingListDto->getPaginationEnabled()) {
            $pager = $this->paginationService->createPaginationForQb($qb);
            $pager->setAllowOutOfRangePages(true);
            $pager->setCurrentPage($listingListDto->getPageNumber());
            if ($pager->getCurrentPage() > $pager->getNbPages()) {
                $listingListDto->setRedirectToPageNumber($pager->getNbPages());
            }

            $listingListDto->setPager($pager);
            $listingListDto->setResults($pager->getCurrentPageResults());
            $listingListDto->setResultsCount($listingListDto->getPagerNotNull()->getNbResults());
        } else {
            $listingListDto->setResults($qb->getQuery()->getResult());
            $listingListDto->setResultsCount(\count((array) $listingListDto->getResults()));
        }

        if ($listingListDto->getSearchQuery()) {
            $this->saveSearchHistory->saveSearch(
                $listingListDto->getSearchQuery(),
                $listingListDto->getResultsCount(),
            );
        }

        return $listingListDto;
    }

    /**
     * @return ListingOnMapDto[]
     */
    public function getListingsOnMap(ListingListDto $listingListDto): array
    {
        if (!$listingListDto->getShowOnMap()) {
            return [];
        }

        $listings = [];
        $this->getListings($listingListDto);

        foreach ($listingListDto->getResults() as $listing) {
            $listings[] = ListingOnMapDto::fromListing($listing);
        }

        return $listings;
    }

    /**
     * @return CustomField[]
     */
    public function getCustomFields(ListingListDto $listingListDto): array
    {
        if (!$listingListDto->getCategory()) {
            return [];
        }

        $qb = $this->em->createQueryBuilder();
        $qb->select('customField');
        $qb->from(CustomField::class, 'customField');
        $qb->addSelect('customFieldOption');
        $qb->join('customField.customFieldForCategories', 'customFieldForCategory');
        $qb->join('customFieldForCategory.category', 'category');
        $qb->leftJoin('customField.customFieldOptions', 'customFieldOption');

        $qb->andWhere($qb->expr()->eq('customField.searchable', 1));
        $qb->andWhere($qb->expr()->eq('category.id', ':category'));
        $qb->setParameter(':category', $listingListDto->getCategory()->getId(), Types::INTEGER);

        $qb->addOrderBy('customFieldForCategory.sort', Criteria::ASC);

        return $qb->getQuery()->getResult();
    }

    public function getListingListDtoFromRequest(Request $request): ListingListDto
    {
        $listingListDto = new ListingListDto();
        $listingListDto->setRoute($request->get('_route'));
        $listingListDto->setCategorySlug($request->get('categorySlug'));
        $listingListDto->setPageNumber((int) $request->get('page', 1));
        $listingListDto->setSearchQuery($request->get('query'));
        $listingListDto->setMinPrice($request->get('minPrice'));
        $listingListDto->setMaxPrice($request->get('maxPrice'));
        $listingListDto->setFilterByCustomFields($request->get(ParamEnum::CUSTOM_FIELD, []));
        $listingListDto->setShowOnMap(BoolHelper::isTrue($request->get('showOnMap')));
        $listingListDto->setMapFullWidth(BoolHelper::isTrue($request->get('mapFullWidth')));
        if ('app_map' === $listingListDto->getRoute()) {
            $listingListDto->setMapFullWidth(true);
            $listingListDto->setShowOnMap(true);
        }
        if ('app_user_observed_listings' === $listingListDto->getRoute() || $request->get('userObserved')) {
            $listingListDto->setFilterByUserObservedListings(true);
        }

        if ($listingListDto->getCategorySlug()) {
            $category = $this->categoryRepository->findOneBy([
                'slug' => $listingListDto->getCategorySlug(),
            ]);
            if (null === $category) {
                throw new NotFoundHttpException();
            }
            $listingListDto->setCategory($category);
        }

        if ($request->query->has('user')) {
            $userId = (int) $request->query->get('user');
            $user = $this->userRepository->findOneBy(['id' => $userId]);
            if (!$user) {
                throw new NotFoundHttpException();
            }

            $listingListDto->setFilterByUser($user);
        }

        if ('app_last_added' === $listingListDto->getRoute()) {
            $listingListDto->setLastAddedList(true);
        }

        return $listingListDto;
    }
}
