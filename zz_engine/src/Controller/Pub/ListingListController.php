<?php

declare(strict_types=1);

namespace App\Controller\Pub;

use App\Enum\ParamEnum;
use App\Service\Advertisement\Dto\AdvertisementDto;
use App\Service\Category\CategoryListService;
use App\Service\Listing\ListingList\ListingListService;
use App\Service\Listing\ListingList\MapWithListings\MapWithListingsService;
use App\Service\Setting\SettingsDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingListController extends AbstractController
{
    /**
     * @var SettingsDto
     */
    private $settingsDto;

    public function __construct(SettingsDto $settingsDto)
    {
        $this->settingsDto = $settingsDto;
    }

    /**
     * @Route("/c/{categorySlug}", name="app_category")
     * @Route("/last-added", name="app_last_added")
     * @Route("/listing/search", name="app_listing_search")
     * @Route("/listings-of-user", name="app_public_listings_of_user")
     * @Route("/map", name="app_map")
     * @Route("/user/observed-listings", name="app_user_observed_listings")
     */
    public function listingList(
        Request $request,
        ListingListService $listingListService,
        MapWithListingsService $mapWithListingsService,
        CategoryListService $categoryListService,
        string $categorySlug = null
    ): Response {
        $mapDefaultConfig = $mapWithListingsService->getDefaultMapConfig($request);
        $listingListDto = $listingListService->getListingListDtoFromRequest($request);
        if ($listingListDto->getRedirectToRoute()) {
            return $this->redirectToRoute($listingListDto->getRedirectToRoute());
        }
        $routeParams = [
            'categorySlug' => $categorySlug,
        ];
        $mapLocationParams = [];

        $category = $listingListDto->getCategory();
        $advertisementDto = new AdvertisementDto();
        $advertisementDto->category = $category;
        $categoryCustomFields = $listingListService->getCustomFields($listingListDto);
        $listingListDto->setCategoryCustomFields($categoryCustomFields);
        if ($listingListDto->getShowOnMap()) {
            $routeParams['showOnMap'] = 1;
            $mapLocationParams['showOnMap'] = 1;
            $mapLocationParams['latitude'] = $request->query->get('latitude');
            $mapLocationParams['longitude'] = $request->query->get('longitude');
            $mapLocationParams['zoom'] = $request->query->get('zoom');
        } else {
            $listingListDto = $listingListService->getListings($listingListDto);
        }
        if ($listingListDto->getMapFullWidth()) {
            $routeParams['mapFullWidth'] = 1;
            $mapLocationParams['mapFullWidth'] = 1;
            $mapLocationParams['latitude'] = $request->query->get('latitude');
            $mapLocationParams['longitude'] = $request->query->get('longitude');
            $mapLocationParams['zoom'] = $request->query->get('zoom');
        }
        if (!$this->settingsDto->getMapEnabled()
            && ($listingListDto->getShowOnMap() || $listingListDto->getMapFullWidth())
        ) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

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

        $queryParameters = [
            'query' => $request->query->get('query'),
            'showOnMap' => $request->query->get('showOnMap', $listingListDto->getMapFullWidth()),
            'mapFullWidth' => $listingListDto->getMapFullWidth(),
            'user' => $request->query->get('user'),
            'minPrice' => $request->query->get('minPrice'),
            'maxPrice' => $request->query->get('maxPrice'),
            ParamEnum::CUSTOM_FIELD => $request->query->get(ParamEnum::CUSTOM_FIELD),
            'latitude' => $request->query->get('latitude'),
            'longitude' => $request->query->get('longitude'),
            'zoom' => $request->query->get('zoom'),
            'categorySlug' => $categorySlug,
            'userObserved' => $listingListDto->getFilterByUserObservedListings(),
        ];
        if (\in_array($queryParameters['showOnMap'], ['0', false, null], true)) {
            unset($queryParameters['showOnMap']);
        }
        if (\in_array($queryParameters['mapFullWidth'], ['0', false, null], true)) {
            unset($queryParameters['mapFullWidth']);
        }

        $template = 'listing_list/listing_list.html.twig';
        if ($listingListDto->getMapFullWidth()) {
            $template = 'listing_list/listing_list_map.html.twig';
        }

        return $this->render(
            $template,
            [
                'listingList' => $listingListDto->getResults(),
                'listingListDto' => $listingListDto,
                'pager' => $listingListDto->getPager(),
                'routeParams' => $routeParams,
                'category' => $category,
                'advertisementDto' => $advertisementDto,
                'categoryBreadcrumbs' => $categoryListService->getCategoriesForBreadcrumbs($category),
                'categoryList' => $categoryListService->getCategoryListForSideMenu($category),
                'customFieldList' => $categoryCustomFields,
                'queryParameters' => $queryParameters,
                'mapLocationParams' => $mapLocationParams,
                ParamEnum::DATA_FOR_JS => [
                    ParamEnum::LISTING_LIST => $listingListService->getListingsOnMap($listingListDto),
                    ParamEnum::MAP_DEFAULT_LATITUDE => $mapDefaultConfig->getLatitude(),
                    ParamEnum::MAP_DEFAULT_LONGITUDE => $mapDefaultConfig->getLongitude(),
                    ParamEnum::MAP_DEFAULT_ZOOM => $mapDefaultConfig->getZoom(),
                ],
            ]
        );
    }
}
