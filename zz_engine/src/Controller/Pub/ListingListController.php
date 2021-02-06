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
     * @Route("/listing/search", name="app_listing_search")
     * @Route("/last-added", name="app_last_added")
     * @Route("/listings-of-user", name="app_public_listings_of_user")
     * @Route("/c/{categorySlug}", name="app_category")
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
        $customFieldsForCategory = $listingListService->getCustomFields($category);
        $listingListDto->setCustomFieldForCategoryList($customFieldsForCategory);
        $listingListDto = $listingListService->getListings($request, $listingListDto);

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
            'listing_list.html.twig',
            [
                'listingList' => $listingListDto->getResults(),
                'pager' => $listingListDto->getPager(),
                'pager_route_params' => $routeParams,
                'listingListDto' => $listingListDto,
                'customFieldList' => $customFieldsForCategory,
                'categoryList' => $categoryListService->getMenuCategoryList($category),
                'categoryBreadcrumbs' => $categoryListService->getCategoryBreadcrumbs($category),
                'category' => $category,
                'queryParameters' => [
                    'query' => $request->query->get('query'),
                    'user' => $request->query->get('user'),
                    'min_price' => $request->query->get('min_price'),
                    'max_price' => $request->query->get('max_price'),
                    ParamEnum::CUSTOM_FIELD => $request->query->get('custom_field'),
                ],
                'pageTitle' => $listingListDto->getPageTitle(),
            ]
        );
    }
}
