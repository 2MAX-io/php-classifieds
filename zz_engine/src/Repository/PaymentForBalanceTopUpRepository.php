<?php

namespace App\Repository;

use App\Entity\PaymentForBalanceTopUp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PaymentForBalanceTopUp|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentForBalanceTopUp|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentForBalanceTopUp[]    findAll()
 * @method PaymentForBalanceTopUp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentForBalanceTopUpRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PaymentForBalanceTopUp::class);
    }
}
