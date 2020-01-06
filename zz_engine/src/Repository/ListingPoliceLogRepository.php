<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ListingPoliceLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ListingPoliceLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListingPoliceLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method ListingPoliceLog[]    findAll()
 * @method ListingPoliceLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListingPoliceLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ListingPoliceLog::class);
    }
}
