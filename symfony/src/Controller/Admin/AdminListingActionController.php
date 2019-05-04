<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\Listing;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminListingActionController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/listing/action/activate/{id}", name="app_admin_listing_activate", methods={"PATCH"})
     */
    public function activate(Request $request, Listing $listing): Response
    {
        $this->denyUnlessAdmin();

        if ($this->isCsrfTokenValid('adminActivate'.$listing->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $listing->setAdminActivated(true);
            $listing->setAdminRejected(false);
            $listing->setAdminLastActivationDate(new \DateTime());
            $entityManager->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/admin/red5/listing/action/remove/{id}", name="app_admin_listing_remove", methods={"DELETE"})
     */
    public function remove(Request $request, Listing $listing): Response
    {
        $this->denyUnlessAdmin();

        if ($this->isCsrfTokenValid('adminRemove'.$listing->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $listing->setAdminRemoved(true);
            $entityManager->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/admin/red5/listing/action/raise/{id}", name="app_admin_listing_raise", methods={"PATCH"})
     */
    public function raise(Request $request, Listing $listing): Response
    {
        $this->denyUnlessAdmin();

        if ($this->isCsrfTokenValid('adminRaise'.$listing->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $listing->setOrderByDate(new \DateTime());
            $entityManager->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
