<?php

declare(strict_types=1);

namespace App\Controller\Admin\Listing;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\Listing;
use App\Form\Admin\AdminRejectListingType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingRejectController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/listing/reject/{id}", name="app_admin_listing_reject")
     */
    public function reject(Request $request, Listing $listing): Response
    {
        $this->denyUnlessAdmin();

        $form = $this->createForm(AdminRejectListingType::class, $listing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $listing->setAdminRejected(true);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute($request->get('_route'), ['id' => $listing->getId()]);
        }

        return $this->render('admin/listing/listing_reject.html.twig', [
            'listing' => $listing,
            'form' => $form->createView(),
        ]);
    }
}
