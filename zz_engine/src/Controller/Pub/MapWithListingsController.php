<?php

declare(strict_types=1);

namespace App\Controller\Pub;

use App\Enum\ParamEnum;
use App\Service\Listing\MapWithListings\MapWithListingsService;
use App\Service\Setting\SettingsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapWithListingsController extends AbstractController
{
    /**
     * @Route("/map", name="app_map_with_listings")
     */
    public function mapWithListings(
        Request $request,
        MapWithListingsService $mapWithListingsService,
        SettingsService $settingsService
    ): Response {
        $mapDefaultConfig = $mapWithListingsService->getDefaultMapConfig($request);

        if (!$settingsService->getSettingsDto()->getMapEnabled()) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        return $this->render('map_with_listings.html.twig', [
            'displayTopAdvert' => false,
            ParamEnum::DATA_FOR_JS => [
                ParamEnum::LISTING_LIST => $mapWithListingsService->getListingsOnMap(),
                ParamEnum::MAP_DEFAULT_LATITUDE => $mapDefaultConfig->getLatitude(),
                ParamEnum::MAP_DEFAULT_LONGITUDE => $mapDefaultConfig->getLongitude(),
                ParamEnum::MAP_DEFAULT_ZOOM => $mapDefaultConfig->getZoom(),
            ],
        ]);
    }
}
