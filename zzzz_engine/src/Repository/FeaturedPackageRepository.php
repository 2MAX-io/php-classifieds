<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\FeaturedPackage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FeaturedPackage|null find($id, $lockMode = null, $lockVersion = null)
 * @method FeaturedPackage|null findOneBy(array $criteria, array $orderBy = null)
 * @method FeaturedPackage[]    findAll()
 * @method FeaturedPackage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeaturedPackageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FeaturedPackage::class);
    }

    // /**
    //  * @return FeaturedPackage[] Returns an array of FeaturedPackage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FeaturedPackage
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
