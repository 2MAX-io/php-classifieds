<?php

declare(strict_types=1);

namespace App\Service\Admin\Other;

use App\Entity\Listing;
use App\Entity\ListingView;
use App\Entity\User;
use App\Service\Admin\Listing\ListingActivateService;
use App\Service\Listing\ListingPublicDisplayService;
use App\System\Cache\AppCacheEnum;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Contracts\Cache\ItemInterface;

class AdminStatsService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ListingActivateService
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
        ListingActivateService $listingActivateListService,
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

    public function getFeaturedListingsCount(): int
    {
        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');
        $qb->select($qb->expr()->count('listing.id'));
        $qb->andWhere($qb->expr()->eq('listing.featured', 1));
        $this->publicDisplayService->applyPublicDisplayConditions($qb);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @Cache
     */
    public function getAllListingsCount(): int
    {
        return $this->cache->get(AppCacheEnum::ADMIN_STATS_LISTINGS_COUNT, function(ItemInterface $item) {
            $item->expiresAfter(300);

            $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');
            $qb->select($qb->expr()->count('listing.id'));

            return (int) $qb->getQuery()->getSingleScalarResult();
        });
    }

    public function getUserCount(): int
    {
        return $this->cache->get(AppCacheEnum::ADMIN_STATS_USERS_COUNT, function(ItemInterface $item) {
            $item->expiresAfter(300);

            $qb = $this->em->getRepository(User::class)->createQueryBuilder('user');
            $qb->select($qb->expr()->count('user.id'));

            return (int) $qb->getQuery()->getSingleScalarResult();
        });
    }

    public function getListingViewsCount(): int
    {
        return $this->cache->get(AppCacheEnum::ADMIN_STATS_VIEWS_COUNT, function(ItemInterface $item) {
            $item->expiresAfter(300);

            $qb = $this->em->getRepository(ListingView::class)->createQueryBuilder('listingView');
            $qb->select('SUM(listingView.viewCount)');

            return (int) $qb->getQuery()->getSingleScalarResult();
        });
    }
}
