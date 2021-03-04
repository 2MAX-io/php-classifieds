<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\FeaturedPackage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository<FeaturedPackage>
 */
class FeaturedPackageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FeaturedPackage::class);
    }
}
