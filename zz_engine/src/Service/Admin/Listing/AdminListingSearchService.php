<?php

declare(strict_types=1);

namespace App\Service\Admin\Listing;

use App\Entity\Category;
use App\Entity\Listing;
use App\Exception\UserVisibleException;
use App\Helper\SearchHelper;
use App\Helper\StringHelper;
use App\Repository\CategoryRepository;
use App\Service\Admin\Listing\Dto\AdminListingListDto;
use App\Service\Listing\ListingPublicDisplayService;
use App\Service\System\Pagination\PaginationService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class AdminListingSearchService
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var ListingPublicDisplayService
     */
    private $listingPublicDisplayService;

    /**
     * @var PaginationService
     */
    private $paginationService;

    /**
     * @var EntityManager|EntityManagerInterface
     */
    private $em;

    public function __construct(
        CategoryRepository $categoryRepository,
        ListingPublicDisplayService $listingPublicDisplayService,
        PaginationService $paginationService,
        EntityManagerInterface $em
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->listingPublicDisplayService = $listingPublicDisplayService;
        $this->paginationService = $paginationService;
        $this->em = $em;
    }

    public function getList(AdminListingListDto $adminListingListDto): AdminListingListDto
    {
        $qb = $this->getQuery($adminListingListDto);

        $pager = $this->paginationService->createPaginationForQb($qb);
        $pager->setMaxPerPage($this->paginationService->getPerPage());
        $pager->setCurrentPage($adminListingListDto->getCurrentPage());

        $adminListingListDto->setResults($pager->getCurrentPageResults());
        $adminListingListDto->setPager($pager);

        return $adminListingListDto;
    }

    public function getQuery(AdminListingListDto $adminListingListDto): QueryBuilder
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('listing');
        $qb->from(Listing::class, 'listing');

        if (!StringHelper::emptyTrim($adminListingListDto->getFilterBySearchQuery())) {
            $qb->andWhere('MATCH (listing.searchText, listing.email, listing.phone, listing.rejectionReason) AGAINST (:query BOOLEAN) > 0');
            $qb->setParameter(':query', SearchHelper::optimizeMatch($adminListingListDto->getFilterBySearchQuery()));
        }

        if (!StringHelper::emptyTrim($adminListingListDto->getFilterByAdminActivated())) {
            $qb->andWhere($qb->expr()->eq('listing.adminActivated', ':adminActivated'));
            $qb->setParameter(':adminActivated', $adminListingListDto->getFilterByAdminActivated());
        }

        if (!StringHelper::emptyTrim($adminListingListDto->getFilterByAdminRejected())) {
            $qb->andWhere($qb->expr()->eq('listing.adminRejected', ':adminRejected'));
            $qb->setParameter(':adminRejected', $adminListingListDto->getFilterByAdminRejected());
        }

        if (!StringHelper::emptyTrim($adminListingListDto->getFilterByAdminRemoved())) {
            $qb->andWhere($qb->expr()->eq('listing.adminRemoved', ':adminRemoved'));
            $qb->setParameter(':adminRemoved', $adminListingListDto->getFilterByAdminRemoved());
        }

        if (!StringHelper::emptyTrim($adminListingListDto->getFilterByUserDeactivated())) {
            $qb->andWhere($qb->expr()->eq('listing.userDeactivated', ':userDeactivated'));
            $qb->setParameter(':userDeactivated', $adminListingListDto->getFilterByUserDeactivated());
        }

        if (!StringHelper::emptyTrim($adminListingListDto->getFilterByUserRemoved())) {
            $qb->andWhere($qb->expr()->eq('listing.userRemoved', ':userRemoved'));
            $qb->setParameter(':userRemoved', $adminListingListDto->getFilterByUserRemoved());
        }

        if (!StringHelper::emptyTrim($adminListingListDto->getFilterByFeatured())) {
            $qb->andWhere($qb->expr()->eq('listing.featured', ':featured'));
            $qb->setParameter(':featured', $adminListingListDto->getFilterByFeatured());
        }

        if (!StringHelper::emptyTrim($adminListingListDto->getFilterByCategory())) {
            $category = $this->categoryRepository->find($adminListingListDto->getFilterByCategory());
            if (!$category) {
                throw new UserVisibleException('category not found');
            }
            $qb->join('listing.category', 'category');
            $qb->andWhere(
                $qb->expr()->andX(
                    $qb->expr()->gte('category.lft', ':requestedCategoryLft'),
                    $qb->expr()->lte('category.rgt', ':requestedCategoryRgt')
                )
            );
            $qb->setParameter(':requestedCategoryLft', $category->getLft());
            $qb->setParameter(':requestedCategoryRgt', $category->getRgt());
        }

        if (!StringHelper::emptyTrim($adminListingListDto->getFilterByUser())) {
            $qb->join('listing.user', 'user');

            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('user.email', ':user'),
                    $qb->expr()->like('user.username', ':user'),
                    $qb->expr()->eq('user.id', ':user')
                )
            );
            $qb->setParameter(':user', SearchHelper::optimizeLike($adminListingListDto->getFilterByUser()));
        }

        if (!StringHelper::emptyTrim($adminListingListDto->getFilterByPublicDisplay())) {
            if ($adminListingListDto->getFilterByPublicDisplay()) {
                $this->listingPublicDisplayService->applyPublicDisplayConditions($qb);
            } else {
                $this->listingPublicDisplayService->applyHiddenConditions($qb);
            }
        }

        $qb->orderBy('listing.id', Criteria::DESC);

        return $qb;
    }

    public function getAdminListingListDtoFromRequest(Request $request): AdminListingListDto
    {
        $adminListingListDto = new AdminListingListDto();
        $adminListingListDto->setCurrentPage((int) $request->query->get('page', 1));
        $adminListingListDto->setFilterBySearchQuery($request->get('query'));
        $adminListingListDto->setFilterByUser($request->get('user'));
        $adminListingListDto->setFilterByCategory($request->get('category'));
        $adminListingListDto->setFilterByPublicDisplay($request->get('publicDisplay'));
        $adminListingListDto->setFilterByFeatured($request->get('featured'));
        $adminListingListDto->setFilterByAdminActivated($request->get('adminActivated'));
        $adminListingListDto->setFilterByAdminRejected($request->get('adminRejected'));
        $adminListingListDto->setFilterByAdminRemoved($request->get('adminRemoved'));
        $adminListingListDto->setFilterByUserDeactivated($request->get('userDeactivated'));
        $adminListingListDto->setFilterByUserRemoved($request->get('userRemoved'));

        return $adminListingListDto;
    }

    /**
     * @return array<int|string,string>
     */
    public function getFilterByCategorySelectList(): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('category');
        $qb->from(Category::class, 'category');
        $qb->andWhere($qb->expr()->gt('category.lvl', 1));
        $qb->addOrderBy('category.sort');
        /** @var Category[] $categories */
        $categories = $qb->getQuery()->getResult();

        $return = [];
        foreach ($categories as $category) {
            $return[$category->getId()] = $category->getPathString();
        }

        return $return;
    }
}
