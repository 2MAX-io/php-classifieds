<?php

declare(strict_types=1);

namespace App\Service\Listing\Featured;

use App\Entity\FeaturedPackage;
use App\Entity\Listing;
use Doctrine\ORM\EntityManagerInterface;

class FeaturedPackageService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(
        EntityManagerInterface $em
    ) {
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

        return $this->getDefaultPlans($listing);
    }

    public function getNotDefaultPlans(Listing $listing): array
    {
        $qb = $this->em->getRepository(FeaturedPackage::class)->createQueryBuilder('featuredPackage');
        $qb->join('featuredPackage.featuredPackageForCategories', 'featuredPackageForCategory');
        $qb->andWhere($qb->expr()->eq('featuredPackageForCategory.category', ':category'));
        $qb->setParameter(':category', $listing->getCategory());
        $qb->andWhere($qb->expr()->eq('featuredPackage.defaultPackage', 0));
        $qb->orderBy('featuredPackage.price', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function getDefaultPlans(Listing $listing): array
    {
        $qb = $this->em->getRepository(FeaturedPackage::class)->createQueryBuilder('featuredPackage');
        $qb->andWhere($qb->expr()->eq('featuredPackage.defaultPackage', 1));
        $qb->orderBy('featuredPackage.price', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
