<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Listing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Listing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Listing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Listing[]    findAll()
 * @method Listing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Listing::class);
    }

    /**
     * @return Listing[]
     */
    public function getFromIds(array $listings): array
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
        $qb->setParameter('listingIds', $listingIds, Connection::PARAM_INT_ARRAY);

        return $qb->getQuery()->getResult();
    }
}
