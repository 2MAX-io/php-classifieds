<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\Listing;
use App\Form\Admin\AdminListingAdvancedEditType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminListingEditAdvancedController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/listing/edit/advanced/{id}", name="app_admin_listing_edit_advanced")
     */
    public function adminListingEditAdvanced(Request $request, Listing $listing): Response
    {
        $this->denyUnlessAdmin();

        $form = $this->createForm(AdminListingAdvancedEditType::class, $listing);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_admin_listing_edit_advanced', [
                'id' => $listing->getId(),
            ]);
        }

        return $this->render('admin/listing/admin_listing_edit_advanced.html.twig', [
            'form' => $form->createView(),
            'listing' => $listing,
        ]);
    }
}
