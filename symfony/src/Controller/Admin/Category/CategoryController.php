<?php

declare(strict_types=1);

namespace App\Controller\Admin\Category;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Service\Admin\Category\CategoryService;
use App\Service\Category\TreeService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/category", name="app_admin_category")
     */
    public function index(CategoryService $categoryService, TreeService $treeService): Response
    {
        $this->denyUnlessAdmin();

        $treeService->rebuild();

        return $this->render('admin/category/index.html.twig', [
            'categoryList' => $categoryService->getCategoryList(),
        ]);
    }

    /**
     * @Route("/admin/red5/category/rebuild", name="app_admin_category_rebuild")
     */
    public function rebuild(TreeService $treeService): Response
    {
        $this->denyUnlessAdmin();

        $treeService->rebuild();

        return new Response('done');
    }
}
