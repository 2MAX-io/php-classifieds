<?php

declare(strict_types=1);

namespace App\Service\Listing\Save;

use App\Entity\Listing;
use App\Entity\ListingFile;
use App\Helper\FileHelper;
use App\Helper\FilePath;
use App\Service\Event\FileModificationEventService;
use App\Service\System\Sort\SortService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Webmozart\PathUtil\Path;

class ListingFileUploadService
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var FileModificationEventService
     */
    private $fileModificationEventService;

    public function __construct(ObjectManager $em, FileModificationEventService $fileModificationEventService)
    {
        $this->em = $em;
        $this->fileModificationEventService = $fileModificationEventService;
    }

    /**
     * @param Listing $listing
     * @param UploadedFile[] $uploadedFileList
     */
    public function addMultipleFilesFromUpload(Listing $listing, array $uploadedFileList): void
    {
        foreach ($uploadedFileList as $uploadedFile) {
            $movedFile = $this->moveFile($uploadedFile, $listing);

            $listingFile = new ListingFile();
            $listingFile->setPath(Path::makeRelative($movedFile->getRealPath(), FilePath::getProjectDir()));
            $listingFile->setListing($listing);
            $listingFile->setFilename(\basename($listingFile->getPath()));
            $listingFile->setMimeType(\mime_content_type($listingFile->getPath()));
            $listingFile->setSizeBytes(\filesize($listingFile->getPath()));
            $listingFile->setSort(SortService::LAST_VALUE);
            $this->em->persist($listingFile);

            $listing->addListingFile($listingFile);
        }
    }

    public function updateSort(Listing $listing, array $fileUploaderList): void
    {
        $filenameIndex = [];
        foreach ($fileUploaderList as $fileUploaderListElement) {
            $fileName = \preg_replace('#(\?.+)$#', '', \basename($fileUploaderListElement['file']));
            $filenameIndex[$fileName] = $fileUploaderListElement['index'];
        }

        foreach ($listing->getListingFiles() as $listingFile) {
            $sort = SortService::LAST_VALUE;
            if (isset($filenameIndex[\basename($listingFile->getPath())])) {
                $sort = $filenameIndex[\basename($listingFile->getPath())];
            }
            $listingFile->setSort((int) $sort);
            $this->em->persist($listingFile);
        }

        $this->fileModificationEventService->updateListingMainImage($listing);
    }

    private function moveFile(UploadedFile $uploadedFile, Listing $listing): File
    {
        FileHelper::throwExceptionIfUnsafeExtension($uploadedFile);

        return $uploadedFile->move(
            $this->getDestinationDirectory($listing),
            $this->getDestinationFilename($uploadedFile)
        );
    }

    private function getDestinationDirectory(Listing $listing): string
    {
        $userDivider = \floor($listing->getUser()->getId() / 10000) + 1;

        return FilePath::getListingFilePath()
            . '/' . $userDivider
            . '/'
            . 'user_'
            . $listing->getUser()->getId()
            . '/'
            . 'listing_' . $listing->getId();
    }

    private function getDestinationFilename(UploadedFile $uploadedFile): string
    {
        return FileHelper::getFilenameValidCharacters($uploadedFile->getClientOriginalName())
            . '.' . $uploadedFile->getClientOriginalExtension();
    }
}
