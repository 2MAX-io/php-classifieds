<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Invoice;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[]    findAll()
 * @method Invoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    /**
     * @return User[]
     */
    public function getListForUser(User $user): array
    {
        $qb = $this->createQueryBuilder('invoice');
        $qb->andWhere($qb->expr()->eq('invoice.user', ':user'));
        $qb->setParameter(':user', $user->getId());

        $qb->addOrderBy('invoice.id', Criteria::DESC);

        return $qb->getQuery()->getResult();
    }
}
