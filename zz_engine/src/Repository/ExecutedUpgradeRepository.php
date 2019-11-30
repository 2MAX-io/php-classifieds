<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ExecutedUpgrade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ExecutedUpgrade|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExecutedUpgrade|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExecutedUpgrade[]    findAll()
 * @method ExecutedUpgrade[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExecutedUpgradeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExecutedUpgrade::class);
    }
}
