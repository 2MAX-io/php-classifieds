<?php

declare(strict_types=1);

namespace App\Controller\Admin\Listing;

use App\Service\Admin\Listing\ListingSearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingSearchController extends AbstractController
{
    /**
     * @Route("/admin/red5/listing/search", name="app_admin_listing_search")
     */
    public function search(ListingSearchService $listingSearchService): Response
    {
        return $this->render('admin/listing/listing_search.html.twig', [
            'listings' => $listingSearchService->getListings(),
        ]);
    }
}