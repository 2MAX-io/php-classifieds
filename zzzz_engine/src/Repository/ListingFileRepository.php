<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ListingFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ListingFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListingFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method ListingFile[]    findAll()
 * @method ListingFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListingFileRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ListingFile::class);
    }

    // /**
    //  * @return ListingFile[] Returns an array of ListingFile objects
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
    public function findOneBySomeField($value): ?ListingFile
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
