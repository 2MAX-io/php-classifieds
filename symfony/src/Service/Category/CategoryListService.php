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
}
