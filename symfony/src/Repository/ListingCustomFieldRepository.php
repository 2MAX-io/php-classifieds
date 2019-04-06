<?php

namespace App\Repository;

use App\Entity\ListingCustomField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ListingCustomField|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListingCustomField|null findOneBy(array $criteria, array $orderBy = null)
 * @method ListingCustomField[]    findAll()
 * @method ListingCustomField[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListingCustomFieldRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ListingCustomField::class);
    }

    // /**
    //  * @return ListingCustomField[] Returns an array of ListingCustomField objects
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
    public function findOneBySomeField($value): ?ListingCustomField
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
