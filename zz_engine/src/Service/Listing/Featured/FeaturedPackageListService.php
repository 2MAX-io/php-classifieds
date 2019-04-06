<?php

declare(strict_types=1);

namespace App\Service\Listing\Featured;

use App\Entity\Listing;
use App\Entity\Package;
use App\Security\CurrentUserService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;

class FeaturedPackageListService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    public function __construct(CurrentUserService $currentUserService, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->currentUserService = $currentUserService;
    }

    /**
     * @return Package[]
     */
    public function getPackages(Listing $listing): array
    {
        $notDefaultPlans = $this->getNotDefaultPlans($listing);
        if (\count($notDefaultPlans) > 0) {
            return $notDefaultPlans;
        }

        return $this->getDefaultPlans();
    }

    /**
     * @return Package[]
     */
    public function getNotDefaultPlans(Listing $listing): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('package');
        $qb->from(Package::class, 'package');
        $qb->join('package.packageForCategories', 'packageForCategory');
        $qb->andWhere($qb->expr()->eq('packageForCategory.category', ':category'));
        $qb->setParameter(':category', $listing->getCategory());
        $qb->andWhere($qb->expr()->eq('package.removed', 0));
        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->gt('package.daysFeaturedExpire', 0),
            $qb->expr()->gt('package.secondsFeaturedExpire', 0),
        ));
        if ($this->currentUserService->getUser()->getDemoPackageUsed()) {
            $qb->andWhere($qb->expr()->neq('package.demoPackage', 1));
        }
        $qb->addOrderBy('package.price', Criteria::ASC);
        $qb->addOrderBy('package.daysListingExpire', Criteria::ASC);
        $qb->addOrderBy('package.daysFeaturedExpire', Criteria::ASC);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Package[]
     */
    public function getDefaultPlans(): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('package');
        $qb->from(Package::class, 'package');
        $qb->andWhere($qb->expr()->eq('package.defaultPackage', 1));
        $qb->andWhere($qb->expr()->eq('package.removed', 0));
        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->gt('package.daysFeaturedExpire', 0),
            $qb->expr()->gt('package.secondsFeaturedExpire', 0),
        ));
        if ($this->currentUserService->getUser()->getDemoPackageUsed()) {
            $qb->andWhere($qb->expr()->neq('package.demoPackage', 1));
        }
        $qb->addOrderBy('package.price', Criteria::ASC);
        $qb->addOrderBy('package.daysListingExpire', Criteria::ASC);
        $qb->addOrderBy('package.daysFeaturedExpire', Criteria::ASC);

        return $qb->getQuery()->getResult();
    }
}
