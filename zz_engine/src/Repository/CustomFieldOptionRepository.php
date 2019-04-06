<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use App\Entity\CustomFieldOption;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository<CustomFieldOption>
 */
class CustomFieldOptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomFieldOption::class);
    }

    /**
     * @param array<int,CustomFieldOption|int|string> $customFieldOptionIds
     *
     * @return Category[]
     */
    public function getFromIds(array $customFieldOptionIds): array
    {
        $ids = [];
        foreach ($customFieldOptionIds as $customFieldOption) {
            if ($customFieldOption instanceof CustomFieldOption) {
                $ids[] = $customFieldOption->getId();
            } else {
                $ids[] = (int) $customFieldOption;
            }
        }

        $qb = $this->createQueryBuilder('customFieldOption');
        $qb->andWhere($qb->expr()->in('customFieldOption.id', ':ids'));
        $qb->setParameter('ids', $ids, Connection::PARAM_INT_ARRAY);
        $qb->indexBy('customFieldOption', 'customFieldOption.id');

        return $qb->getQuery()->getResult();
    }
}
