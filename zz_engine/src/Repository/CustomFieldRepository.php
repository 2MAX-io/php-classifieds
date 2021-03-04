<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use App\Entity\CustomField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository<CustomField>
 */
class CustomFieldRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomField::class);
    }

    /**
     * @param array<int,CustomField|int|string> $customFieldIds
     *
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
        $qb->setParameter('ids', $ids, Connection::PARAM_INT_ARRAY);
        $qb->indexBy('customField', 'customField.id');

        return $qb->getQuery()->getResult();
    }
}
