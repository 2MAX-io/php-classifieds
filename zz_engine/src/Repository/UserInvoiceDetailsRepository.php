<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserInvoiceDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserInvoiceDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserInvoiceDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserInvoiceDetails[]    findAll()
 * @method UserInvoiceDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
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

    // /**
    //  * @return UserInvoiceDetails[] Returns an array of UserInvoiceDetails objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserInvoiceDetails
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
