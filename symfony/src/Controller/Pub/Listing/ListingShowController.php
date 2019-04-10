<?php

declare(strict_types=1);

namespace App\Controller\Pub\Listing;

use App\Service\Listing\ShowSingle\ListingShowSingleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingShowController extends AbstractController
{
    /**
     * @Route("/listing/show/{id}", name="app_listing_show")
     */
    public function show(int $id, ListingShowSingleService $listingShowSingleService): Response
    {
        return $this->render(
            'listing_show.html.twig',
            [
                'listing' => $listingShowSingleService->getSingle($id),
            ]
        );
    }
}
