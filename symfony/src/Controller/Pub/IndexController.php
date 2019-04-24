<?php

declare(strict_types=1);

namespace App\Controller\Pub;

use App\Service\Index\CategoryListService;
use App\Service\Listing\Helper\ListingHelperService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index(
        CategoryListService $categoryListService,
        ListingHelperService $listingHelperService
    ): Response {
        return $this->render('index.html.twig', [
            'categoryList' => $categoryListService->getCategoryList(),
            'latestListings' => $listingHelperService->getLatestListings(8),
            'recommendedListings' => $listingHelperService->getRecommendedListings(8),
        ]);
    }
}
