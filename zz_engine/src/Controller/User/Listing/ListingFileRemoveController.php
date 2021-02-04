<?php

declare(strict_types=1);

namespace App\Controller\User\Listing;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\ListingFile;
use App\Service\Event\FileModificationEventService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingFileRemoveController extends AbstractUserController
{
    /**
     * @Route("/user/listing/file/remove", name="app_listing_file_remove", methods={"POST"}, options={"expose": true})
     */
    public function remove(
        Request $request,
        FileModificationEventService $fileModificationEventService,
        LoggerInterface $logger
    ): Response {
        $fileId = $request->request->get('listingFileId');
        $listingFile = $this->getDoctrine()->getRepository(ListingFile::class)->find($fileId);
        if (!$listingFile) {
            $logger->error('can not remove, listing file with id `{listingFileId}` not found', [
                'listingFileId' => $fileId,
            ]);

            return $this->json('listing file not found');
        }
        $this->dennyUnlessCurrentUserAllowed($listingFile->getListing());

        $listingFile->setUserRemoved(true);
        $fileModificationEventService->onFileModification($listingFile);

        $em = $this->getDoctrine()->getManager();
        $em->persist($listingFile);
        $em->flush();

        return $this->json([]);
    }
}
