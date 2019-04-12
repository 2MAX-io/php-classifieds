<?php

declare(strict_types=1);

namespace App\Controller\Admin\Listing;

use App\Entity\Listing;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingPoliceLogController extends AbstractController
{
    /**
     * @Route("/admin/red5/listing/police-log/{id}", name="app_admin_police_log")
     */
    public function policeLog(Listing $listing): Response
    {
        return $this->render('admin/listing/listing_police_log.html.twig', [
            'listing' => $listing,
        ]);
    }
}
