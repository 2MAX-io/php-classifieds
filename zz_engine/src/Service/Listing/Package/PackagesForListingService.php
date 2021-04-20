<?php

declare(strict_types=1);

namespace App\Service\Listing\Package;

use App\Entity\Category;
use App\Entity\Listing;
use App\Entity\Package;
use App\Entity\PackageForCategory;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;

class PackagesForListingService
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
    public function getPackages(Category $category = null): array
    {
        if (null === $category) {
            return $this->getDefaultPlans();
        }

        $notDefaultPlans = $this->getPackagesForCategory($category);
        if (\count($notDefaultPlans) > 0) {
            return $notDefaultPlans;
        }

        return $this->getDefaultPlans();
    }

    public function isPackageForListing(Listing $listing, Package $package): bool
    {
        if ($package->getDefaultPackage() && !$this->haveCategorySpecificPackage($listing->getCategory())) {
            return true; // we use default package
        }

        $qb = $this->em->createQueryBuilder();
        $qb->select('packageForCategory');
        $qb->from(PackageForCategory::class, 'packageForCategory');
        $qb->select($qb->expr()->count('packageForCategory.id'));
        $qb->join('packageForCategory.package', 'package');
        $qb->andWhere($qb->expr()->eq('packageForCategory.package', ':package'));
        $qb->andWhere($qb->expr()->eq('packageForCategory.category', ':category'));
        $qb->andWhere($qb->expr()->eq('package.removed', 0));
        $qb->setParameter(':package', $package->getId(), Types::INTEGER);
        $qb->setParameter(':category', $listing->getCategory());

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * @return Package[]
     */
    private function getPackagesForCategory(Category $category): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('package');
        $qb->from(Package::class, 'package');
        $qb->join('package.packageForCategories', 'packageForCategory');
        $qb->andWhere($qb->expr()->eq('packageForCategory.category', ':category'));
        $qb->setParameter(':category', $category->getId());
        $qb->andWhere($qb->expr()->eq('package.defaultPackage', 0));
        $qb->andWhere($qb->expr()->eq('package.removed', 0));
        $qb->andWhere($qb->expr()->eq('package.demoPackage', 0));
        $qb->addOrderBy('package.price', Criteria::DESC);
        $qb->addOrderBy('package.daysListingExpire', Criteria::ASC);
        $qb->addOrderBy('package.daysFeaturedExpire', Criteria::ASC);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Package[]
     */
    private function getDefaultPlans(): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('package');
        $qb->from(Package::class, 'package');
        $qb->andWhere($qb->expr()->eq('package.defaultPackage', 1));
        $qb->andWhere($qb->expr()->eq('package.removed', 0));
        $qb->andWhere($qb->expr()->eq('package.demoPackage', 0));
        $qb->addOrderBy('package.price', Criteria::ASC);
        $qb->addOrderBy('package.daysListingExpire', Criteria::ASC);
        $qb->addOrderBy('package.daysFeaturedExpire', Criteria::ASC);

        return $qb->getQuery()->getResult();
    }

    private function haveCategorySpecificPackage(Category $category): bool
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('packageForCategory');
        $qb->from(PackageForCategory::class, 'packageForCategory');
        $qb->select($qb->expr()->count('packageForCategory.id'));
        $qb->join('packageForCategory.package', 'package');
        $qb->andWhere($qb->expr()->eq('packageForCategory.category', ':category'));
        $qb->andWhere($qb->expr()->eq('package.defaultPackage', 0));
        $qb->andWhere($qb->expr()->eq('package.removed', 0));
        $qb->setParameter(':category', $category->getId(), Types::INTEGER);

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }
}
