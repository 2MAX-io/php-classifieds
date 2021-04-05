<?php

declare(strict_types=1);

namespace App\Service\Listing\Featured;

use App\Entity\Listing;
use App\Entity\Package;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;

class PackageService
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
        $qb->andWhere($qb->expr()->eq('package.defaultPackage', 0));
        $qb->andWhere($qb->expr()->eq('package.removed', 0));
        $qb->orderBy('package.price', Criteria::ASC);

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
        $qb->orderBy('package.price', Criteria::ASC);

        return $qb->getQuery()->getResult();
    }
}
