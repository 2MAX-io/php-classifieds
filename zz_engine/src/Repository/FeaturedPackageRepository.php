<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\FeaturedPackage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FeaturedPackage|null find($id, $lockMode = null, $lockVersion = null)
 * @method FeaturedPackage|null findOneBy(array $criteria, array $orderBy = null)
 * @method FeaturedPackage[]    findAll()
 * @method FeaturedPackage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeaturedPackageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FeaturedPackage::class);
    }
}
