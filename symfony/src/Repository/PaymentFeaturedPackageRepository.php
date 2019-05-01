<?php

namespace App\Repository;

use App\Entity\PaymentFeaturedPackage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PaymentFeaturedPackage|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentFeaturedPackage|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentFeaturedPackage[]    findAll()
 * @method PaymentFeaturedPackage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentFeaturedPackageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PaymentFeaturedPackage::class);
    }

    // /**
    //  * @return PaymentFeaturedPackage[] Returns an array of PaymentFeaturedPackage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PaymentFeaturedPackage
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}