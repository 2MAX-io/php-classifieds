<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserInvoiceDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository<UserInvoiceDetails>
 */
class UserInvoiceDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserInvoiceDetails::class);
    }

    public function findByUser(User $user): ?UserInvoiceDetails
    {
        $qb = $this->createQueryBuilder('user_invoice_details');
        $qb->andWhere($qb->expr()->eq('user_invoice_details.user', ':user'));
        $qb->setParameter('user', $user->getId());
        $qb->orderBy('user_invoice_details.id', Criteria::DESC);
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
