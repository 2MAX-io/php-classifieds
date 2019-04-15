<?php

declare(strict_types=1);

namespace App\Controller\Pub\Listing;

use App\Security\CurrentUserService;
use App\Service\Category\CategoryListService;
use App\Service\Listing\ListingPublicDisplayService;
use App\Service\Listing\ShowSingle\ListingShowSingleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingShowController extends AbstractController
{
    /**
     * @Route("/listing/show/{id}", name="app_listing_show")
     */
    public function show(
        int $id,
        ListingShowSingleService $listingShowSingleService,
        CategoryListService $categoryListService,
        CurrentUserService $currentUserService,
        ListingPublicDisplayService $listingPublicDisplayService
    ): Response {
        $listingShowDto = $listingShowSingleService->getSingle($id);
        if (!$listingShowDto) {
            throw $this->createNotFoundException();
        }

        if ($listingShowDto->getListing()->getUser() !== $currentUserService->getUser()) {
            if (!$listingPublicDisplayService->canPublicDisplay($listingShowDto->getListing())) {
                throw $this->createNotFoundException();
            }
        }

        $listingShowSingleService->saveView($listingShowDto->getListing());

        return $this->render(
            'listing_show.html.twig',
            [
                'listingShowDto' => $listingShowDto,
                'listing' => $listingShowDto->getListing(),
                'categoryBreadcrumbs' => $categoryListService->getBreadcrumbs($listingShowDto->getListing()->getCategory()),
            ]
        );
    }
}
