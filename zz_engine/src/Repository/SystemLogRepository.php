<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SystemLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SystemLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method SystemLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method SystemLog[]    findAll()
 * @method SystemLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SystemLogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SystemLog::class);
    }
}
