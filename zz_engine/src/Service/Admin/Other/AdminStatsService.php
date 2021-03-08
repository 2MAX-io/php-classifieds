<?php

declare(strict_types=1);

namespace App\Service\Admin\Other;

use App\Entity\Listing;
use App\Entity\ListingView;
use App\Entity\System\ListingReport;
use App\Entity\User;
use App\Enum\AppCacheEnum;
use App\Helper\DateHelper;
use App\Service\Admin\Listing\ListingActivateService;
use App\Service\Listing\ListingPublicDisplayService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Contracts\Cache\CacheInterface;
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
        ListingActivateService $listingActivateListService,
        ListingPublicDisplayService $publicDisplayService,
        CacheInterface $cache,
        EntityManagerInterface $em
    ) {
        $this->em = $em;
        $this->listingActivateListService = $listingActivateListService;
        $this->publicDisplayService = $publicDisplayService;
        $this->cache = $cache;
    }

    public function getToActivateCount(): int
    {
        $qb = $this->listingActivateListService->getAwaitingActivationQueryBuilder();
        $qb->select($qb->expr()->count('listing.id'));
        $qb->resetDQLPart('orderBy');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getReportedListingsCount(): int
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select($qb->expr()->count('listingReport.id'));
        $qb->from(ListingReport::class, 'listingReport');
        $qb->andWhere($qb->expr()->eq('listingReport.removed', 0));

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getAddedLastHours(int $hours): int
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('listing');
        $qb->from(Listing::class, 'listing');
        $qb->select($qb->expr()->count('listing.id'));
        $qb->andWhere($qb->expr()->gte('listing.firstCreatedDate', ':listingsFrom'));
        $qb->setParameter('listingsFrom', DateHelper::carbonNow()->subHours($hours)->setTime(1, 0));

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getActiveListingsCount(): int
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('listing');
        $qb->from(Listing::class, 'listing');
        $qb->select($qb->expr()->count('listing.id'));
        $this->publicDisplayService->applyPublicDisplayConditions($qb);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getFeaturedListingsCount(): int
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('listing');
        $qb->from(Listing::class, 'listing');
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
        return $this->cache->get(AppCacheEnum::ADMIN_STATS_LISTINGS_COUNT, function (ItemInterface $item) {
            $item->expiresAfter(300);

            $qb = $this->em->createQueryBuilder();
            $qb->select('listing');
            $qb->from(Listing::class, 'listing');
            $qb->select($qb->expr()->count('listing.id'));

            return (int) $qb->getQuery()->getSingleScalarResult();
        });
    }

    public function getUserCount(): int
    {
        return $this->cache->get(AppCacheEnum::ADMIN_STATS_USERS_COUNT, function (ItemInterface $item) {
            $item->expiresAfter(300);

            $qb = $this->em->createQueryBuilder();
            $qb->select('user');
            $qb->from(User::class, 'user');
            $qb->select($qb->expr()->count('user.id'));

            return (int) $qb->getQuery()->getSingleScalarResult();
        });
    }

    public function getListingViewsCount(): int
    {
        return $this->cache->get(AppCacheEnum::ADMIN_STATS_VIEWS_COUNT, function (ItemInterface $item) {
            $item->expiresAfter(300);

            $qb = $this->em->createQueryBuilder();
            $qb->select('listingView');
            $qb->from(ListingView::class, 'listingView');
            $qb->select('SUM(listingView.viewCount)');

            return (int) $qb->getQuery()->getSingleScalarResult();
        });
    }
}
