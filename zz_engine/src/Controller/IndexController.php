<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\Category\CategoryListService;
use App\Service\Listing\Secondary\RecentListingsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @copyright 2MAX.io Classified Ads
 *
 * @see https://2max.io
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function indexForPublic(
        CategoryListService $categoryListService,
        RecentListingsService $recentListingsService
    ): Response {
        return $this->render('index.html.twig', [
            'categoryList' => $categoryListService->getMainCategoryList(),
            'latestListings' => $recentListingsService->getLatestListings(8),
            'recommendedListings' => $recentListingsService->getRecommendedListings(8),
        ]);
    }
}
