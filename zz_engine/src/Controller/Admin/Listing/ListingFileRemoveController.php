<?php

declare(strict_types=1);

namespace App\Controller\Admin\Listing;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\ListingFile;
use App\Service\Listing\Save\OnListingFileModificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

class ListingFileRemoveController extends AbstractAdminController
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
     * @Route("/admin/red5/listing/listing-file/{id}/action/remove-listing-file", name="app_admin_listing_file_remove", methods={"DELETE"})
     */
    public function removeListingFile(
        Request $request,
        ListingFile $listingFile,
        OnListingFileModificationService $onListingFileModificationService
    ): Response {
        $this->denyUnlessAdmin();

        if (!$this->isCsrfTokenValid('csrf_adminRemoveListingFile'.$listingFile->getId(), $request->request->get('_token'))) {
            throw new InvalidCsrfTokenException('token not valid');
        }
        $listingFile->setUserRemoved(true);
        $onListingFileModificationService->onFileModification($listingFile);
        $this->em->flush();

        return $this->redirectToRoute('app_admin_listing_edit', [
            'id' => $listingFile->getListingNotNull()->getId(),
        ]);
    }
}
