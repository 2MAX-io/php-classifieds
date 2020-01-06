<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function getRootNode(): Category
    {
        $qb =  $this->createQueryBuilder('category');
        $qb->andWhere($qb->expr()->isNull('category.parent'));
        $qb->orderBy('category.id', 'ASC');

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
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
        $qb->setParameter('ids', $ids, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY);
        $qb->indexBy('category', 'category.id');

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Category[]
     */
    public function getFlatCategoryList(): array
    {
        $qb = $this->createQueryBuilder('category');
        $qb->andWhere($qb->expr()->gt('category.lvl', 0));

        $qb->addOrderBy('category.sort', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
