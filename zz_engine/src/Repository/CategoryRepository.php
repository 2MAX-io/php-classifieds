<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function getRootNode(): Category
    {
        $qb = $this->createQueryBuilder('category');
        $qb->andWhere($qb->expr()->isNull('category.parent'));
        $qb->orderBy('category.id', Criteria::ASC);
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param array<int,Category|int|string> $categoryIds
     *
     * @return Category[]
     */
    public function getFromIds(array $categoryIds): array
    {
        $ids = [];
        foreach ($categoryIds as $category) {
            if ($category instanceof Category) {
                $ids[] = $category->getId();
            } else {
                $ids[] = (int) $category;
            }
        }

        $qb = $this->createQueryBuilder('category');
        $qb->andWhere($qb->expr()->in('category.id', ':ids'));
        $qb->setParameter('ids', $ids, Connection::PARAM_INT_ARRAY);
        $qb->indexBy('category', 'category.id');

        return $qb->getQuery()->getResult();
    }
}
