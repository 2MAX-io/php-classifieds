<?php

namespace App\Repository;

use App\Entity\ListingView;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ListingView|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListingView|null findOneBy(array $criteria, array $orderBy = null)
 * @method ListingView[]    findAll()
 * @method ListingView[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListingViewRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ListingView::class);
    }

    // /**
    //  * @return ListingView[] Returns an array of ListingView objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ListingView
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
