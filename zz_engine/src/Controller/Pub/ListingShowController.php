<?php

declare(strict_types=1);

namespace App\Controller\Pub;

use App\Enum\ParamEnum;
use App\Service\Category\CategoryListService;
use App\Service\Listing\ListingPublicDisplayService;
use App\Service\Listing\ShowSingle\ListingShowSingleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ListingShowController extends AbstractController
{
    /**
     * @Route("/l/{id}/{slug}", name="app_listing_show", options={"expose": true})
     */
    public function singleListingShow(
        Request $request,
        int $id,
        string $slug,
        ListingShowSingleService $listingShowSingleService,
        CategoryListService $categoryListService,
        ListingPublicDisplayService $listingPublicDisplayService,
        EventDispatcherInterface $eventDispatcher
    ): Response {
        $listingShowDto = $listingShowSingleService->getSingle($id);
        if (!$listingShowDto) {
            throw $this->createNotFoundException();
        }
        $listing = $listingShowDto->getListing();

        $slugDifferentThanExpected = $slug !== $listing->getSlug();
        if ($slugDifferentThanExpected) {
            return $this->redirectToRoute($request->get('_route'), [
                'id' => $id,
                'slug' => $listing->getSlug(),
            ]);
        }

        /*
         * can not display view, removed
         */
        if (!$listingPublicDisplayService->canDisplay($listing)) {
            return $this->render( // render listing removed view
                'listing_show_can_not_display.html.twig',
                [
                    'listingShowDto' => $listingShowDto,
                    'listing' => $listing,
                    'category' => $listing->getCategory(),
                    'categoryBreadcrumbs' => $categoryListService->getCategoriesForBreadcrumbs(
                        $listing->getCategory(),
                    ),
                    ParamEnum::DATA_FOR_JS => [
                        ParamEnum::SHOW_LISTING_PREVIEW_FOR_OWNER => $request->query->get(ParamEnum::SHOW_LISTING_PREVIEW_FOR_OWNER),
                    ],
                ]
            );
        }

        $eventDispatcher->addListener(
            KernelEvents::TERMINATE,
            static function () use ($listingShowSingleService, $listingShowDto): void {
                $listingShowSingleService->saveView($listingShowDto->getListing());
            }
        );

        return $this->render(
            'listing_show.html.twig',
            [
                'listingShowDto' => $listingShowDto,
                'listing' => $listing,
                'category' => $listing->getCategory(),
                'categoryBreadcrumbs' => $categoryListService->getCategoriesForBreadcrumbs(
                    $listing->getCategory(),
                ),
                ParamEnum::DATA_FOR_JS => [
                    ParamEnum::SHOW_LISTING_PREVIEW_FOR_OWNER => $request->query->get(ParamEnum::SHOW_LISTING_PREVIEW_FOR_OWNER),
                    ParamEnum::MAP_LOCATION_COORDINATES => [
                        ParamEnum::LONGITUDE => $listing->getLocationLongitude(),
                        ParamEnum::LATITUDE => $listing->getLocationLatitude(),
                    ],
                ],
            ]
        );
    }

    /**
     * @Route(
     *     "/private/listing/show-contact-data",
     *     name="app_listing_contact_data",
     *     methods={"POST"},
     *     options={"expose"=true},
     * )
     */
    public function contactDataShow(
        Request $request,
        ListingShowSingleService $listingShowSingleService,
        ListingPublicDisplayService $listingPublicDisplayService,
        Environment $twig
    ): Response {
        $listingId = (int) $request->request->get(ParamEnum::LISTING_ID);
        $listingShowDto = $listingShowSingleService->getSingle($listingId);
        if (!$listingShowDto) {
            throw $this->createNotFoundException();
        }

        if (!$listingPublicDisplayService->canDisplay($listingShowDto->getListing())) {
            throw $this->createAccessDeniedException();
        }

        return $this->json([
            ParamEnum::SUCCESS => true,
            ParamEnum::SHOW_CONTACT_HTML => $twig->render('listing_show_contact.html.twig', [
                'listing' => $listingShowDto->getListing(),
            ]),
        ]);
    }
}
