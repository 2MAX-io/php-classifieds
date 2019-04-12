<?php

declare(strict_types=1);

namespace App\Controller\Admin\Listing;

use App\Entity\Listing;
use App\Form\Admin\AdminRejectListingType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingRejectController extends AbstractController
{
    /**
     * @Route("/admin/red5/listing-reject/{id}", name="app_admin_listing_reject")
     */
    public function reject(Request $request, Listing $listing): Response
    {
        $form = $this->createForm(AdminRejectListingType::class, $listing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $listing->setAdminRejected(true);

            $this->getDoctrine()->getManager()->flush();
        }
        return $this->render('admin/listing/listing_reject.html.twig', [
            'listing' => $listing,
            'form' => $form->createView(),
        ]);
    }
}
