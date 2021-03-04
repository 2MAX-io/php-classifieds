<?php

declare(strict_types=1);

namespace App\Controller\Pub;

use App\Enum\ParamEnum;
use App\Service\Category\CategoryListService;
use App\Service\Listing\ListingList\ListingListService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingListController extends AbstractController
{
    /**
     * @Route("/c/{categorySlug}", name="app_category")
     * @Route("/last-added", name="app_last_added")
     * @Route("/listing/search", name="app_listing_search")
     * @Route("/listings-of-user", name="app_public_listings_of_user")
     */
    public function listingList(
        Request $request,
        ListingListService $listingListService,
        CategoryListService $categoryListService,
        string $categorySlug = null
    ): Response {
        $listingListDto = $listingListService->getListingListDtoFromRequest($request);
        if ($listingListDto->getRedirectToRoute()) {
            return $this->redirectToRoute($listingListDto->getRedirectToRoute());
        }
        $routeParams = [
            'categorySlug' => $categorySlug,
        ];

        $category = $listingListDto->getCategory();
        $categoryCustomFields = $listingListService->getCustomFields($listingListDto);
        $listingListDto->setCategoryCustomFields($categoryCustomFields);
        $listingListDto = $listingListService->getListings($listingListDto);

        if ($listingListDto->getRedirectToPageNumber()) {
            return $this->redirectToRoute(
                $listingListDto->getRoute(),
                \array_replace(
                    $routeParams,
                    $request->query->all(),
                    [
                        'page' => $listingListDto->getRedirectToPageNumber(),
                    ]
                ),
                Response::HTTP_TEMPORARY_REDIRECT
            );
        }

        return $this->render(
            'listing_list/listing_list.html.twig',
            [
                'listingList' => $listingListDto->getResults(),
                'listingListDto' => $listingListDto,
                'pager' => $listingListDto->getPager(),
                'route_params' => $routeParams,
                'category' => $category,
                'categoryBreadcrumbs' => $categoryListService->getCategoriesForBreadcrumbs($category),
                'categoryList' => $categoryListService->getCategoryListForSideMenu($category),
                'customFieldList' => $categoryCustomFields,
                'queryParameters' => [
                    'query' => $request->query->get('query'),
                    'user' => $request->query->get('user'),
                    'minPrice' => $request->query->get('minPrice'),
                    'maxPrice' => $request->query->get('maxPrice'),
                    ParamEnum::CUSTOM_FIELD => $request->query->get(ParamEnum::CUSTOM_FIELD),
                ],
            ]
        );
    }
}
