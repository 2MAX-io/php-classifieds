<?php

declare(strict_types=1);

namespace App\Controller\Pub;

use App\Service\Index\CategoryListService;
use App\Service\Index\ListingListForIndexService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @copyright 2MAX.io Classified Ads
 * @link https://2max.io
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index(
        CategoryListService $categoryListService,
        ListingListForIndexService $listingListForIndexService
    ): Response {
        return $this->render('index.html.twig', [
            'categoryList' => $categoryListService->getCategoryList(),
            'latestListings' => $listingListForIndexService->getLatestListings(8),
            'recommendedListings' => $listingListForIndexService->getRecommendedListings(8),
        ]);
    }
}
