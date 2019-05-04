<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Listing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Listing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Listing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Listing[]    findAll()
 * @method Listing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListingRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Listing::class);
    }

    /**
     * @return Listing[]
     */
    public function getListingsFromIds(array $listings): array
    {
        $listingIds = [];
        foreach ($listings as $listing) {
            if ($listing instanceof Listing) {
                $listingIds[] = $listing->getId();
            } else {
                $listingIds[] = (int) $listing;
            }
        }

        $qb = $this->createQueryBuilder('listing');
        $qb->andWhere($qb->expr()->in('listing.id', ':listingIds'));
        $qb->setParameter('listingIds', $listingIds, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY);

        return $qb->getQuery()->getResult();
    }

    // /**
    //  * @return Listing[] Returns an array of Listing objects
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
    public function findOneBySomeField($value): ?Listing
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
