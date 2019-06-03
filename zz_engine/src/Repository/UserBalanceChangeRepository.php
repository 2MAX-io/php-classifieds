<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserBalanceChange;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserBalanceChange|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserBalanceChange|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserBalanceChange[]    findAll()
 * @method UserBalanceChange[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserBalanceChangeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserBalanceChange::class);
    }
}
