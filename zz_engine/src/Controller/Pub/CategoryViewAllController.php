<?php

declare(strict_types=1);

namespace App\Controller\Pub;

use App\Service\Category\CategoryViewAllService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryViewAllController extends AbstractController
{
    /**
     * @Route("/categories", name="app_category_view_all")
     */
    public function categoriesViewAll(CategoryViewAllService $categoryViewAllService): Response
    {
        return $this->render('secondary/category_view_all.html.twig', [
            'categories' => $categoryViewAllService->getAllCategories(),
        ]);
    }
}
