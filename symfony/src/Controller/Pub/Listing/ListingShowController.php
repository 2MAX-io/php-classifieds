<?php

declare(strict_types=1);

namespace App\Controller\Pub\Listing;

use App\Entity\Listing;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingShowController extends AbstractController
{
    /**
     * @Route("/listing/show/{id}", name="app_listing_show")
     */
    public function show(Listing $listing): Response
    {
        return $this->render(
            'listing_show.html.twig',
            [
                'listing' => $listing,
            ]
        );
    }
}
