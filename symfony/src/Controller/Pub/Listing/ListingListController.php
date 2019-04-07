<?php

declare(strict_types=1);

namespace App\Controller\Pub\Listing;

use App\Service\Listing\ListingList\ListingListService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingListController extends AbstractController
{
    /**
     * @Route("/listing/list", name="app_listing_list")
     */
    public function index(ListingListService $listingListService): Response
    {
        return $this->render(
            'listing_list.html.twig',
            [
                'listingList' => $listingListService->getListings(),
                'customFieldList' => $listingListService->getCustomFields(),
            ]
        );
    }
}
