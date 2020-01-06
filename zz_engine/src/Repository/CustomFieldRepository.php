<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use App\Entity\CustomField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CustomField|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomField|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomField[]    findAll()
 * @method CustomField[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomFieldRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
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
}
