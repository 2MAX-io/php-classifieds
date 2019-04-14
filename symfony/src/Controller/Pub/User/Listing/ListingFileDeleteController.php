<?php

namespace App\Controller\Pub\User\Listing;

use App\Controller\Pub\User\Base\AbstractUserController;
use App\Entity\ListingFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingFileDeleteController extends AbstractUserController
{
    /**
     * @Route("/user/listing/file/delete", name="app_listing_file_delete", methods={"POST"})
     */
    public function index(Request $request): Response
    {
        $fileId = $request->request->get('listingFileId');
        $listingFile = $this->getDoctrine()->getRepository(ListingFile::class)->find($fileId);
        $this->dennyUnlessCurrentUserListing($listingFile->getListing());

        $listingFile->setUserDeleted(true);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($listingFile);
        $entityManager->flush();

        return $this->json([]);
    }
}
