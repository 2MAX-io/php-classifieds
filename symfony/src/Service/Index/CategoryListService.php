<?php

declare(strict_types=1);

namespace App\Service\Index;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;

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
    public function getCategoryList()
    {
        $qb = $this->em->getRepository(Category::class)->createQueryBuilder('category');

        return $qb->getQuery()->getResult();
    }
}
