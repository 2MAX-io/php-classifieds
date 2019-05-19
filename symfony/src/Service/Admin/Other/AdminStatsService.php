<?php

declare(strict_types=1);

namespace App\Service\Admin\Other;

use App\Entity\Listing;
use App\Entity\ListingView;
use App\Entity\User;
use App\Service\Admin\Listing\ListingActivateListService;
use App\Service\Listing\ListingPublicDisplayService;
use App\System\Cache\AppCacheInterface;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Psr\SimpleCache\CacheInterface;

class AdminStatsService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ListingActivateListService
     */
    private $listingActivateListService;

    /**
     * @var ListingPublicDisplayService
     */
    private $publicDisplayService;

    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(
        EntityManagerInterface $em,
        ListingActivateListService $listingActivateListService,
        ListingPublicDisplayService $publicDisplayService,
        CacheInterface $cache
    ) {
        $this->em = $em;
        $this->listingActivateListService = $listingActivateListService;
        $this->publicDisplayService = $publicDisplayService;
        $this->cache = $cache;
    }

    public function getToActivateCount(): int
    {
        $qb = $this->listingActivateListService->getQueryBuilder();
        $qb->select($qb->expr()->count('listing.id'));
        $qb->resetDQLPart('orderBy');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getAddedLastHours(int $hours): int
    {
        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');
        $qb->select($qb->expr()->count('listing.id'));
        $qb->andWhere($qb->expr()->gte('listing.firstCreatedDate', ':listingsFrom'));
        $qb->setParameter('listingsFrom', Carbon::now()->subHours($hours)->setTime(1, 0, 0));

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getActiveListingsCount(): int
    {
        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');
        $qb->select($qb->expr()->count('listing.id'));
        $this->publicDisplayService->applyPublicDisplayConditions($qb);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getAllListingsCount(): int
    {
        $cacheName = AppCacheInterface::ADMIN_STATS_LISTINGS_COUNT;
        if ($this->cache->has($cacheName)) {
            return $this->cache->get($cacheName);
        }

        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');
        $qb->select($qb->expr()->count('listing.id'));

        $return = (int) $qb->getQuery()->getSingleScalarResult();
        $this->cache->set($cacheName, $return, 300);

        return $return;
    }

    public function getUserCount(): int
    {
        $cacheName = AppCacheInterface::ADMIN_STATS_USERS_COUNT;
        if ($this->cache->has($cacheName)) {
            return $this->cache->get($cacheName);
        }

        $qb = $this->em->getRepository(User::class)->createQueryBuilder('user');
        $qb->select($qb->expr()->count('user.id'));


        $return = (int) $qb->getQuery()->getSingleScalarResult();
        $this->cache->set($cacheName, $return, 300);

        return $return;
    }

    public function getFeaturedListingsCount(): int
    {
        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');
        $qb->select($qb->expr()->count('listing.id'));
        $qb->andWhere($qb->expr()->eq('listing.featured', 1));
        $this->publicDisplayService->applyPublicDisplayConditions($qb);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getListingViewsCount(): int
    {
        $cacheName = AppCacheInterface::ADMIN_STATS_VIEWS_COUNT;
        if ($this->cache->has($cacheName)) {
            return $this->cache->get($cacheName);
        }

        $qb = $this->em->getRepository(ListingView::class)->createQueryBuilder('listingView');
        $qb->select('SUM(listingView.viewCount)');

        $return = (int) $qb->getQuery()->getSingleScalarResult();
        $this->cache->set($cacheName, $return, 300);

        return $return;
    }
}
