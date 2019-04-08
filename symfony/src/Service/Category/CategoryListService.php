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
    public function getLevelOfSubcategoriesToDisplayForCategory(int $categoryId): array
    {
        $qb = $this->em->getRepository(Category::class)->createQueryBuilder('category');
        $qb->join(Category::class, 'requestedCategory', Join::WITH, $qb->expr()->eq('requestedCategory.id', ':requestedCategory'));
        $qb->setParameter(':requestedCategory', $categoryId);

        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->eq('category.parent', 'requestedCategory.id'),
                $qb->expr()->andX(
                    $qb->expr()->eq('requestedCategory.rgt - requestedCategory.lft', '1'),
                    $qb->expr()->eq('category.parent', 'requestedCategory.parent')
                )
            )
        );

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Category[]
     */
    public function getBreadcrumbs(Category $category): array
    {
        $qb = $this->em->getRepository(Category::class)->createQueryBuilder('category0');
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
}