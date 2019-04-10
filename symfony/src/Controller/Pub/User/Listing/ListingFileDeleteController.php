<?php

namespace App\Controller\Pub\User\Listing;

use App\Entity\ListingFile;
use App\Security\CurrentUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ListingFileDeleteController extends AbstractController
{
    /**
     * @Route("/user/listing/file/delete", name="app_listing_file_delete", methods={"POST"})
     */
    public function index(Request $request, CurrentUserService $currentUserService): Response
    {
        $fileId = $request->request->get('listingFileId');
        $listingFile = $this->getDoctrine()->getRepository(ListingFile::class)->find($fileId);

        if ($currentUserService->getUser() !== $listingFile->getListing()->getUser()) {
            throw new UnauthorizedHttpException('user of listing do not match current user');
        }

        $listingFile->setUserDeleted(true);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($listingFile);
        $entityManager->flush();

        return $this->json([]);
    }
}
