<?php

namespace App\Repository;

use App\Entity\ExecutedUpgrade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ExecutedUpgrade|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExecutedUpgrade|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExecutedUpgrade[]    findAll()
 * @method ExecutedUpgrade[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExecutedUpgradeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ExecutedUpgrade::class);
    }

    // /**
    //  * @return ExecutedUpgrade[] Returns an array of ExecutedUpgrade objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ExecutedUpgrade
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}