<?php

declare(strict_types=1);

namespace App\Controller\Admin\Listing;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingRejectController extends AbstractController
{
    /**
     * @Route("/admin/red5/listing-reject/{id}", name="app_admin_listing_reject")
     */
    public function reject(): Response
    {
        return $this->render('admin/listing/listing_reject.html.twig', [
        ]);
    }
}
