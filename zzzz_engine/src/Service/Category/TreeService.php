<?php

declare(strict_types=1);

namespace App\Service\Category;

use App\Entity\Category;
use App\Service\Admin\Category\AdminCategoryService;
use Doctrine\ORM\EntityManagerInterface;
use StefanoTree\NestedSet;

class TreeService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var AdminCategoryService
     */
    private $adminCategoryService;

    public function __construct(EntityManagerInterface $em, AdminCategoryService $adminCategoryService)
    {
        $this->em = $em;
        $this->adminCategoryService = $adminCategoryService;
    }

    public function rebuild()
    {
        $tree = new NestedSet([
            'tableName' => 'category',
            'idColumnName' => 'id',
            'levelColumnName' => 'lvl',
        ], $this->em->getConnection());

        $tree->rebuild($this->getRootNode()->getId());
        $this->adminCategoryService->reorderSort();
    }

    private function getRootNode(): Category
    {
        return $this->em->getRepository(Category::class)->getRootNode();
    }
}
