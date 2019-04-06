<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\Listing;
use App\Form\Admin\AdminListingAdvancedEditType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminListingEditAdvancedController extends AbstractAdminController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/admin/red5/listing/edit/advanced/{id}", name="app_admin_listing_edit_advanced")
     */
    public function listingEditAdvancedForAdmin(Request $request, Listing $listing): Response
    {
        $this->denyUnlessAdmin();

        $form = $this->createForm(AdminListingAdvancedEditType::class, $listing);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->redirectToRoute('app_admin_listing_edit_advanced', [
                'id' => $listing->getId(),
            ]);
        }

        return $this->render('admin/listing/edit/admin_listing_edit_advanced.html.twig', [
            'form' => $form->createView(),
            'listing' => $listing,
        ]);
    }
}
