<?php

declare(strict_types=1);

namespace App\Controller\Admin\Category;

use App\Service\Category\TreeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/admin/red5/category", name="app_admin_category")
     */
    public function index(TreeService $treeService): Response
    {
        $treeService->rebuild();

        return new Response('done');
    }
}
