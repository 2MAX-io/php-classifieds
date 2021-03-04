<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Invoice;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository<Invoice>
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    /**
     * @return Invoice[]
     */
    public function getListForUser(User $user): array
    {
        $qb = $this->createQueryBuilder('invoice');
        $qb->andWhere($qb->expr()->eq('invoice.user', ':user'));
        $qb->setParameter(':user', $user->getId());

        $qb->addOrderBy('invoice.id', Criteria::DESC);
        $qb->setMaxResults(100);

        return $qb->getQuery()->getResult();
    }
}
