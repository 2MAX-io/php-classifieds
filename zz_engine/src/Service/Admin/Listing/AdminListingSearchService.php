<?php

declare(strict_types=1);

namespace App\Service\Admin\Listing;

use App\Entity\Category;
use App\Entity\Listing;
use App\Helper\Search;
use App\Helper\Str;
use App\Service\System\Pagination\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class AdminListingSearchService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var PaginationService
     */
    private $paginationService;

    public function __construct(
        EntityManagerInterface $em,
        RequestStack $requestStack,
        PaginationService $paginationService
    ) {
        $this->em = $em;
        $this->requestStack = $requestStack;
        $this->paginationService = $paginationService;
    }

    /**
     * @return Listing[]
     */
    public function getList(int $page): AdminListingListDto
    {
        $qb = $this->getQuery();

        $pager = $this->paginationService->createPaginationForQb($qb);
        $pager->setMaxPerPage($this->paginationService->getMaxPerPage());
        $pager->setCurrentPage($page);

        $adminListingListDto = new AdminListingListDto($pager->getCurrentPageResults(), $pager);

        return $adminListingListDto;
    }

    public function getQuery(): QueryBuilder
    {
        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');
        /** @var Request $request */
        $request = $this->requestStack->getMasterRequest();

        if (!Str::emptyTrim($request->get('query'))) {
            $qb->andWhere('MATCH (listing.searchText, listing.email, listing.phone, listing.rejectionReason) AGAINST (:query BOOLEAN) > 0');
            $qb->setParameter(':query', Search::optimizeMatch($request->get('query')));
        }

        if (!Str::emptyTrim($request->get('adminActivated'))) {
            $qb->andWhere($qb->expr()->eq('listing.adminActivated', ':adminActivated'));
            $qb->setParameter(':adminActivated', $request->get('adminActivated'));
        }

        if (!Str::emptyTrim($request->get('adminRejected'))) {
            $qb->andWhere($qb->expr()->eq('listing.adminRejected', ':adminRejected'));
            $qb->setParameter(':adminRejected', $request->get('adminRejected'));
        }

        if (!Str::emptyTrim($request->get('adminRemoved'))) {
            $qb->andWhere($qb->expr()->eq('listing.adminRemoved', ':adminRemoved'));
            $qb->setParameter(':adminRemoved', $request->get('adminRemoved'));
        }

        if (!Str::emptyTrim($request->get('userDeactivated'))) {
            $qb->andWhere($qb->expr()->eq('listing.userDeactivated', ':userDeactivated'));
            $qb->setParameter(':userDeactivated', $request->get('userDeactivated'));
        }

        if (!Str::emptyTrim($request->get('userRemoved'))) {
            $qb->andWhere($qb->expr()->eq('listing.userRemoved', ':userRemoved'));
            $qb->setParameter(':userRemoved', $request->get('userRemoved'));
        }

        if (!Str::emptyTrim($request->get('featured'))) {
            $qb->andWhere($qb->expr()->eq('listing.featured', ':featured'));
            $qb->setParameter(':featured', $request->get('featured'));
        }

        if (!Str::emptyTrim($request->get('category'))) {
            $category = $this->em->getRepository(Category::class)->find($request->get('category'));
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

        if (!Str::emptyTrim($request->get('user'))) {
            $qb->join('listing.user', 'user');

            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('user.email', ':user'),
                    $qb->expr()->like('user.username', ':user'),
                    $qb->expr()->eq('user.id', ':user')
                )
            );
            $qb->setParameter(':user', Search::optimizeLike($request->get('user')));
        }

        $qb->orderBy('listing.id', 'DESC');

        return $qb;
    }

    public function getCategories(): array
    {
        $qb = $this->em->getRepository(Category::class)->createQueryBuilder('category');
        $qb->addOrderBy('category.sort');
        /** @var Category[] $categories */
        $categories = $qb->getQuery()->getResult();

        $return = [];
        foreach ($categories as $category) {
            $path = \array_map(
                static function (Category $category) {
                    if ($category->getLvl() < 1) {
                        return false;
                    }

                    return $category->getName();
                },
                $category->getPath()
            );
            $return[$category->getId()] = \implode(' ⇾ ', $path);
        }

        return $return;
    }
}
