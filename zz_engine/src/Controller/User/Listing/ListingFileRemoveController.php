<?php

declare(strict_types=1);

namespace App\Controller\User\Listing;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\ListingFile;
use App\Service\Event\FileModificationEventService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingFileRemoveController extends AbstractUserController
{
    /**
     * @Route("/user/listing/file/remove", name="app_listing_file_remove", methods={"POST"})
     */
    public function remove(Request $request, FileModificationEventService $fileModificationEventService): Response
    {
        $fileId = $request->request->get('listingFileId');
        $listingFile = $this->getDoctrine()->getRepository(ListingFile::class)->find($fileId);
        $this->dennyUnlessCurrentUserAllowed($listingFile->getListing());

        $listingFile->setUserRemoved(true);
        $fileModificationEventService->onFileModification($listingFile);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($listingFile);
        $entityManager->flush();

        return $this->json([]);
    }
}
