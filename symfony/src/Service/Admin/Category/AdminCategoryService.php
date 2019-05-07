<?php

declare(strict_types=1);

namespace App\Service\Admin\Category;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;

class AdminCategoryService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return Category[]
     */
    public function getCategoryList(): array
    {
        $qb = $this->em->getRepository(Category::class)->createQueryBuilder('category1');
        $qb->addSelect('category2');
        $qb->addSelect('category3');
        $qb->leftJoin('category1.children', 'category2');
        $qb->leftJoin('category2.children', 'category3');
        $qb->andWhere($qb->expr()->eq('category1.lvl', 1));

        $qb->addOrderBy('category1.sort', 'ASC');
        $qb->addOrderBy('category2.sort', 'ASC');
        $qb->addOrderBy('category3.sort', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function reorderSort(): void
    {
        $categoryRepository = $this->em->getRepository(Category::class);
        $qb = $categoryRepository->createQueryBuilder('category1');
        $qb->addSelect('category2');
        $qb->addSelect('category3');
        $qb->addSelect('category4');
        $qb->leftJoin('category1.children', 'category2');
        $qb->leftJoin('category2.children', 'category3');
        $qb->leftJoin('category3.children', 'category4');
        $qb->andWhere($qb->expr()->eq('category1.id', $categoryRepository->getRootNode()->getId()));

        $qb->addOrderBy('category1.sort', 'ASC');
        $qb->addOrderBy('category2.sort', 'ASC');
        $qb->addOrderBy('category3.sort', 'ASC');
        $qb->addOrderBy('category4.sort', 'ASC');

        /** @var Category $rootCategory */
        $rootCategory = $qb->getQuery()->getSingleResult();

        $rootCategory->setSort(0);
        $this->reorderCategoryAndChildren($rootCategory, $rootCategory->getSort());

//        $pdo = $this->em->getConnection();
//        $pdo->query('SET @count = 0');
//        $pdo->query('UPDATE `category` SET sort = parent_id*1000 + @count:= @count + 1 WHERE 1 ORDER BY lft ASC, sort ASC;');
    }

    private function reorderCategoryAndChildren(Category $parentCategory, int $baseSort = null)
    {
        static $sort;
        if ($baseSort !== null) {
            $sort = $baseSort;
        }
        foreach ($parentCategory->getChildren() as $category) {
            $sort++;
            $category->setSort($sort);
            $this->em->persist($category);

            if ($category->getChildren()) {
                $this->reorderCategoryAndChildren($category);
            }
        }
    }
}