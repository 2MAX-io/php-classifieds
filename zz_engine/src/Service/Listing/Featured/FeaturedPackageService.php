<?php

declare(strict_types=1);

namespace App\Service\Listing\Featured;

use App\Entity\FeaturedPackage;
use App\Entity\Listing;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;

class FeaturedPackageService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return FeaturedPackage[]
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
     * @return FeaturedPackage[]
     */
    public function getNotDefaultPlans(Listing $listing): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('featuredPackage');
        $qb->from(FeaturedPackage::class, 'featuredPackage');
        $qb->join('featuredPackage.featuredPackageForCategories', 'featuredPackageForCategory');
        $qb->andWhere($qb->expr()->eq('featuredPackageForCategory.category', ':category'));
        $qb->setParameter(':category', $listing->getCategory());
        $qb->andWhere($qb->expr()->eq('featuredPackage.defaultPackage', 0));
        $qb->andWhere($qb->expr()->eq('featuredPackage.removed', 0));
        $qb->orderBy('featuredPackage.price', Criteria::ASC);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return FeaturedPackage[]
     */
    public function getDefaultPlans(): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('featuredPackage');
        $qb->from(FeaturedPackage::class, 'featuredPackage');
        $qb->andWhere($qb->expr()->eq('featuredPackage.defaultPackage', 1));
        $qb->andWhere($qb->expr()->eq('featuredPackage.removed', 0));
        $qb->orderBy('featuredPackage.price', Criteria::ASC);

        return $qb->getQuery()->getResult();
    }
}
