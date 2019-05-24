<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use App\Entity\CustomFieldJoinCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CustomFieldJoinCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomFieldJoinCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomFieldJoinCategory[]    findAll()
 * @method CustomFieldJoinCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomFieldJoinCategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CustomFieldJoinCategory::class);
    }

    /**
     * @return Category[]
     */
    public function getFromIds(array $customFieldJoinCategoryIds): array
    {
        $ids = [];
        foreach ($customFieldJoinCategoryIds as $customFieldJoinCategory) {
            if ($customFieldJoinCategory instanceof CustomFieldJoinCategory) {
                $ids[] = $customFieldJoinCategory->getId();
            } else {
                $ids[] = (int) $customFieldJoinCategory;
            }
        }

        $qb = $this->createQueryBuilder('customFieldJoinCategory');
        $qb->andWhere($qb->expr()->in('customFieldJoinCategory.id', ':ids'));
        $qb->setParameter('ids', $ids, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY);
        $qb->indexBy('customFieldJoinCategory', 'customFieldJoinCategory.id');

        return $qb->getQuery()->getResult();
    }
}
