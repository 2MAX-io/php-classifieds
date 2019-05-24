<?php

declare(strict_types=1);

namespace App\Service\Category;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;

class CategoryListService
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
    public function getLevelOfSubcategoriesToDisplayForCategory(?Category $category = null): array
    {
        $qb = $this->em->getRepository(Category::class)->createQueryBuilder('category');

        if ($category === null) {
            $qb->andWhere($qb->expr()->eq('category.lvl', 1));
        }

        if ($category !== null) {
            $qb->join(
                Category::class,
                'requestedCategory',
                Join::WITH,
                $qb->expr()->eq('requestedCategory.id', ':requestedCategory')
            );
            $qb->setParameter(':requestedCategory', $category->getId());

            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->eq('category.parent', 'requestedCategory.id'),
                    $qb->expr()->andX(
                        $qb->expr()->eq('requestedCategory.rgt - requestedCategory.lft', '1'),
                        $qb->expr()->eq('category.parent', 'requestedCategory.parent')
                    )
                )
            );
        }

        $qb->orderBy('category.sort', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Category[]
     */
    public function getBreadcrumbs(?Category $category = null): array
    {
        if ($category === null) {
            return [];
        }

        $qb = $this->em->getRepository(Category::class)->createQueryBuilder('category0');
        $qb->addSelect('category1');
        $qb->addSelect('category2');
        $qb->addSelect('category3');
        $qb->addSelect('category4');
        $qb->addSelect('category5');
        $qb->addSelect('category6');
        $qb->addSelect('category7');
        $qb->addSelect('category8');
        $qb->addSelect('category9');

        $qb->leftJoin('category0.parent', 'category1');
        $qb->leftJoin('category1.parent', 'category2');
        $qb->leftJoin('category2.parent', 'category3');
        $qb->leftJoin('category3.parent', 'category4');
        $qb->leftJoin('category4.parent', 'category5');
        $qb->leftJoin('category5.parent', 'category6');
        $qb->leftJoin('category6.parent', 'category7');
        $qb->leftJoin('category7.parent', 'category8');
        $qb->leftJoin('category8.parent', 'category9');

        $qb->andWhere($qb->expr()->eq('category0.id', ':category'));
        $qb->setParameter(':category', $category);

        /** @var Category $category */
        $category = $qb->getQuery()->getOneOrNullResult();

        $result = [$category];
        while($category) {
            $category = $category->getParent();
            if ($category && $category->getLvl() > 0) {
                $result[] = $category;
            }
        }

        return array_reverse($result);
    }

    /**
     * @return Category[]
     */
    public function getFormCategorySelectList(): array
    {
        $qb = $this->em->getRepository(Category::class)->createQueryBuilder('category');
        $qb->addSelect('categoryChildren');
        $qb->leftJoin('category.children', 'categoryChildren');
        $qb->andWhere($qb->expr()->neq('category.rgt - category.lft', '1'));
        /** @var Category[] $categoryList */
        $categoryList = $qb->getQuery()->getResult();

        $result = [];
        foreach ($categoryList as $category) {
            $result[$category->getLvl()][] = $category;
        }

        return $result;
    }
}
