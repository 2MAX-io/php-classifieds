<?php

declare(strict_types=1);

namespace App\Controller\Admin\Listing;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\ListingFile;
use App\Service\Event\FileModificationEventService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingFileRemoveController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/listing/file-remove/{id}", name="app_admin_listing_file_remove", methods={"DELETE"})
     */
    public function remove(Request $request, ListingFile $listingFile, FileModificationEventService $fileModificationEventService): Response
    {
        $this->denyUnlessAdmin();

        if ($this->isCsrfTokenValid('adminRemoveFile'.$listingFile->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $listingFile->setUserRemoved(true);
            $fileModificationEventService->onFileModification($listingFile);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_listing_edit', ['id' => $listingFile->getListing()->getId()]);
    }
}
