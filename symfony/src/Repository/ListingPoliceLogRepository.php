<?php

namespace App\Repository;

use App\Entity\ListingPoliceLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ListingPoliceLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListingPoliceLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method ListingPoliceLog[]    findAll()
 * @method ListingPoliceLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListingPoliceLogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ListingPoliceLog::class);
    }

    // /**
    //  * @return Log[] Returns an array of Log objects
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
    public function findOneBySomeField($value): ?Log
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