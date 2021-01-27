<?php

declare(strict_types=1);

namespace App\Controller\Pub;

use App\Enum\ParamEnum;
use App\Service\Category\CategoryListService;
use App\Service\Listing\ListingPublicDisplayService;
use App\Service\Listing\ShowSingle\ListingShowSingleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

class ListingShowController extends AbstractController
{
    /**
     * @Route("/l/{id}/{slug}", name="app_listing_show", options={"expose": true})
     */
    public function showSingleListing(
        Request $request,
        int $id,
        string $slug,
        ListingShowSingleService $listingShowSingleService,
        CategoryListService $categoryListService,
        ListingPublicDisplayService $listingPublicDisplayService,
        EventDispatcherInterface $eventDispatcher
    ): Response {
        $listingShowDto = $listingShowSingleService->getSingle($id);
        $listing = $listingShowDto->getListing();
        if (!$listingShowDto) {
            throw $this->createNotFoundException();
        }

        if ($slug !== $listing->getSlug()) {
            return $this->redirectToRoute($request->get('_route'), [
                'id' => $id,
                'slug' => $listing->getSlug(),
            ]);
        }

        if (!$listingPublicDisplayService->canDisplay($listing)) {
            // render listing removed view
            return $this->render(
                'listing_show_when_removed.html.twig',
                [
                    'listingShowDto' => $listingShowDto,
                    'listing' => $listing,
                    'category' => $listing->getCategory(),
                    'categoryBreadcrumbs' => $categoryListService->getBreadcrumbs(
                        $listing->getCategory()
                    ),
                    ParamEnum::DATA_FOR_JS => [
                        'showPreviewForOwner' => $request->query->get('showPreviewForOwner'),
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
                'categoryBreadcrumbs' => $categoryListService->getBreadcrumbs(
                    $listing->getCategory()
                ),
                ParamEnum::DATA_FOR_JS => [
                    'showPreviewForOwner' => $request->query->get('showPreviewForOwner'),
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
    public function showContactData(
        Request $request,
        ListingShowSingleService $listingShowSingleService,
        ListingPublicDisplayService $listingPublicDisplayService,
        Environment $twig
    ): Response {
        $listingId = (int) $request->request->get('listingId');
        $listingShowDto = $listingShowSingleService->getSingle($listingId);
        if (!$listingShowDto) {
            throw $this->createNotFoundException();
        }

        if (!$listingPublicDisplayService->canDisplay($listingShowDto->getListing(), true)) {
            throw $this->createAccessDeniedException();
        }

        return $this->json([
            'success' => true,
            'html' => $twig->render('listing_show_contact.html.twig', [
                'listing' => $listingShowDto->getListing(),
            ])
        ]);
    }
}
