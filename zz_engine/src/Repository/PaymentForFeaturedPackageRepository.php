<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\PaymentForFeaturedPackage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PaymentForFeaturedPackage|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentForFeaturedPackage|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentForFeaturedPackage[]    findAll()
 * @method PaymentForFeaturedPackage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentForFeaturedPackageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PaymentForFeaturedPackage::class);
    }
}
