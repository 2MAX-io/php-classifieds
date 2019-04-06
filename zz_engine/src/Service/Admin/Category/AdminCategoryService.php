<?php

declare(strict_types=1);

namespace App\Service\Admin\Category;

use App\Entity\Category;
use App\Enum\SortConfig;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use StefanoTree\NestedSet;

class AdminCategoryService
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(CategoryRepository $categoryRepository, EntityManagerInterface $em)
    {
        $this->categoryRepository = $categoryRepository;
        $this->em = $em;
    }

    /**
     * @return Category[]
     */
    public function getCategoriesWithChildren(): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('category1');
        $qb->from(Category::class, 'category1');
        $qb->addSelect('category2');
        $qb->addSelect('category3');
        $qb->addSelect('category4');
        $qb->addSelect('category5');
        $qb->addSelect('category6');
        $qb->addSelect('category7');
        $qb->addSelect('category8');
        $qb->addSelect('category9');
        $qb->leftJoin('category1.children', 'category2');
        $qb->leftJoin('category2.children', 'category3');
        $qb->leftJoin('category3.children', 'category4');
        $qb->leftJoin('category4.children', 'category5');
        $qb->leftJoin('category5.children', 'category6');
        $qb->leftJoin('category6.children', 'category7');
        $qb->leftJoin('category7.children', 'category8');
        $qb->leftJoin('category8.children', 'category9');

        $qb->addOrderBy('category1.sort', Criteria::ASC);
        $qb->addOrderBy('category2.sort', Criteria::ASC);
        $qb->addOrderBy('category3.sort', Criteria::ASC);
        $qb->addOrderBy('category4.sort', Criteria::ASC);
        $qb->addOrderBy('category5.sort', Criteria::ASC);
        $qb->addOrderBy('category6.sort', Criteria::ASC);
        $qb->addOrderBy('category7.sort', Criteria::ASC);
        $qb->addOrderBy('category8.sort', Criteria::ASC);
        $qb->addOrderBy('category9.sort', Criteria::ASC);

        $qb->andWhere($qb->expr()->eq('category1.lvl', 1));

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int[] $orderedCategoryIdList
     */
    public function saveOrder(array $orderedCategoryIdList): void
    {
        $categories = $this->categoryRepository->getFromIds($orderedCategoryIdList);

        $sort = 1;
        foreach ($orderedCategoryIdList as $categoryId) {
            $category = $categories[$categoryId];
            $category->setSort($sort);
            $this->em->persist($category);
            ++$sort;
        }
    }

    public function resetOrderOfAllCategories(): void
    {
        $rootNodeCategory = $this->categoryRepository->getRootNode();
        $tree = new NestedSet([
            'tableName' => 'category',
            'idColumnName' => 'id',
            'levelColumnName' => 'lvl',
        ], $this->em->getConnection());

        $tree->rebuild($rootNodeCategory->getId());

        $qb = $this->categoryRepository->createQueryBuilder('category1');
        $qb->addSelect('category2');
        $qb->addSelect('category3');
        $qb->addSelect('category4');
        $qb->addSelect('category5');
        $qb->addSelect('category6');
        $qb->addSelect('category7');
        $qb->addSelect('category8');
        $qb->addSelect('category9');
        $qb->leftJoin('category1.children', 'category2');
        $qb->leftJoin('category2.children', 'category3');
        $qb->leftJoin('category3.children', 'category4');
        $qb->leftJoin('category4.children', 'category5');
        $qb->leftJoin('category5.children', 'category6');
        $qb->leftJoin('category6.children', 'category7');
        $qb->leftJoin('category7.children', 'category8');
        $qb->leftJoin('category8.children', 'category9');

        $qb->addOrderBy('category1.sort', Criteria::ASC);
        $qb->addOrderBy('category2.sort', Criteria::ASC);
        $qb->addOrderBy('category3.sort', Criteria::ASC);
        $qb->addOrderBy('category4.sort', Criteria::ASC);
        $qb->addOrderBy('category5.sort', Criteria::ASC);
        $qb->addOrderBy('category6.sort', Criteria::ASC);
        $qb->addOrderBy('category7.sort', Criteria::ASC);
        $qb->addOrderBy('category8.sort', Criteria::ASC);
        $qb->addOrderBy('category9.sort', Criteria::ASC);

        $qb->andWhere($qb->expr()->eq('category1.id', $rootNodeCategory->getId()));

        /** @var Category $rootCategoryWithChildren */
        $rootCategoryWithChildren = $qb->getQuery()->getSingleResult();
        $rootCategoryWithChildren->setSort(0);

        $sortOrderValue = SortConfig::START_REORDER_FROM;
        $this->resetOrderOfCategoryAndChildren($rootCategoryWithChildren, $sortOrderValue);
    }

    private function resetOrderOfCategoryAndChildren(
        Category $parentCategory,
        int &$sortOrderValue = SortConfig::START_REORDER_FROM
    ): void {
        foreach ($parentCategory->getChildren() as $category) {
            /** @var int $sortOrderValue */
            ++$sortOrderValue;
            $category->setSort($sortOrderValue);
            $this->em->persist($category);

            if ($category->getChildren()->count()) {
                $this->resetOrderOfCategoryAndChildren($category, $sortOrderValue);
            }
        }
    }
}
