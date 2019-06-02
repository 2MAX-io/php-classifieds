<?php

declare(strict_types=1);

namespace App\Controller\Pub;

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
     * @Route("/l/{id}/{slug}", name="app_listing_show")
     */
    public function show(
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

        if (!$listingPublicDisplayService->canDisplay($listingShowDto->getListing())) {
            return $this->render(
                'listing_show_when_removed.html.twig',
                [
                    'listingShowDto' => $listingShowDto,
                    'listing' => $listingShowDto->getListing(),
                    'categoryBreadcrumbs' => $categoryListService->getBreadcrumbs(
                        $listingShowDto->getListing()->getCategory()
                    ),
                ]
            );
        }

        if ($slug !== $listingShowDto->getListing()->getSlug()) {
            return $this->redirectToRoute($request->get('_route'), [
                'id' => (int) $id,
                'slug' => $listingShowDto->getListing()->getSlug(),
            ]);
        }

        $eventDispatcher->addListener(
            KernelEvents::TERMINATE,
            function () use ($listingShowSingleService, $listingShowDto) {
                $listingShowSingleService->saveView($listingShowDto->getListing());
            }
        );

        return $this->render(
            'listing_show.html.twig',
            [
                'listingShowDto' => $listingShowDto,
                'listing' => $listingShowDto->getListing(),
                'categoryBreadcrumbs' => $categoryListService->getBreadcrumbs(
                    $listingShowDto->getListing()->getCategory()
                ),
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

        if (!$listingPublicDisplayService->canDisplay($listingShowDto->getListing())) {
            throw $this->createAccessDeniedException();
        }

        return $this->json([
            'html' => $twig->render('listing_show_contact.html.twig', [
                'listing' => $listingShowDto->getListing(),
            ])
        ]);
    }
}
