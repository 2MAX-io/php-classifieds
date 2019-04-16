<?php

declare(strict_types=1);

namespace App\Service\Category;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use StefanoTree\NestedSet;

class TreeService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function rebuild()
    {
        $tree = new NestedSet([
            'tableName' => 'category',
            'idColumnName' => 'id',
            'levelColumnName' => 'lvl',
        ], $this->em->getConnection());

        $tree->rebuild($this->getRootNode()->getId());
    }

    private function getRootNode(): Category
    {
        $qb =  $this->em->getRepository(Category::class)->createQueryBuilder('category');
        $qb->andWhere($qb->expr()->isNull('category.parent'));
        $qb->orderBy('category.id', 'ASC');

        return $qb->getQuery()->getOneOrNullResult();
    }
}
