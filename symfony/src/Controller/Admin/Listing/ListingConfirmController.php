<?php

declare(strict_types=1);

namespace App\Controller\Admin\Listing;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingConfirmController extends AbstractController
{
    /**
     * @Route("/admin/red5/listing-confirm", name="app_admin_listing_confirm")
     */
    public function listingConfirm(): Response
    {
        return $this->render('admin/listing/listing_confirm.html.twig', []);
    }
}
