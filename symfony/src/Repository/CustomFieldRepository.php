<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use App\Entity\CustomField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CustomField|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomField|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomField[]    findAll()
 * @method CustomField[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomFieldRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CustomField::class);
    }

    /**
     * @return Category[]
     */
    public function getFromIds(array $customFieldIds): array
    {
        $ids = [];
        foreach ($customFieldIds as $customField) {
            if ($customField instanceof CustomField) {
                $ids[] = $customField->getId();
            } else {
                $ids[] = (int) $customField;
            }
        }

        $qb = $this->createQueryBuilder('customField');
        $qb->andWhere($qb->expr()->in('customField.id', ':ids'));
        $qb->setParameter('ids', $ids, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY);
        $qb->indexBy('customField', 'customField.id');

        return $qb->getQuery()->getResult();
    }

    // /**
    //  * @return CustomField[] Returns an array of CustomField objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CustomField
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
