<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CustomFieldForCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository<CustomFieldForCategory>
 */
class CustomFieldForCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomFieldForCategory::class);
    }

    /**
     * @param array<int,CustomFieldForCategory|int|string> $customFieldForCategoryIds
     *
     * @return CustomFieldForCategory[]
     */
    public function getFromIds(array $customFieldForCategoryIds): array
    {
        $ids = [];
        foreach ($customFieldForCategoryIds as $customFieldForCategory) {
            if ($customFieldForCategory instanceof CustomFieldForCategory) {
                $ids[] = $customFieldForCategory->getId();
            } else {
                $ids[] = (int) $customFieldForCategory;
            }
        }

        $qb = $this->createQueryBuilder('customFieldForCategory');
        $qb->andWhere($qb->expr()->in('customFieldForCategory.id', ':ids'));
        $qb->setParameter('ids', $ids, Connection::PARAM_INT_ARRAY);
        $qb->indexBy('customFieldForCategory', 'customFieldForCategory.id');

        return $qb->getQuery()->getResult();
    }
}
