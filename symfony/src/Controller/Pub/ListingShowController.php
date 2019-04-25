<?php

declare(strict_types=1);

namespace App\Controller\Pub;

use App\Security\CurrentUserService;
use App\Service\Category\CategoryListService;
use App\Service\Listing\ListingPublicDisplayService;
use App\Service\Listing\ShowSingle\ListingShowSingleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        CurrentUserService $currentUserService,
        ListingPublicDisplayService $listingPublicDisplayService
    ): Response {
        $listingShowDto = $listingShowSingleService->getSingle($id);
        if (!$listingShowDto) {
            throw $this->createNotFoundException();
        }

        $forceDisplay = $listingShowDto->getListing()->getUser() === $currentUserService->getUser() || $currentUserService->lowSecurityCheckIsAdminInPublic();
        if (!$forceDisplay && !$listingPublicDisplayService->canPublicDisplay($listingShowDto->getListing())) {
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

        $listingShowSingleService->saveView($listingShowDto->getListing());

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
}
