<?php

declare(strict_types=1);

namespace App\Service\Admin\Category;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;

class CategoryService
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

        return $qb->getQuery()->getResult();
    }
}
