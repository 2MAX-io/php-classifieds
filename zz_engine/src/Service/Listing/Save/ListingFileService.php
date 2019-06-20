<?php

declare(strict_types=1);

namespace App\Service\Listing\Save;

use App\Entity\Listing;
use App\Entity\ListingFile;
use App\Helper\FileHelper;
use App\Helper\FilePath;
use App\Service\Event\FileModificationEventService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\File;
use Webmozart\PathUtil\Path;

class ListingFileService
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

    public function processListingFiles(Listing $listing, array $fileUploaderList): void
    {
        $sortForExisting = [];
        foreach ($fileUploaderList as $fileUploaderListElement) {
            if (isset($fileUploaderListElement['data']['listingFileId'])) {
                $listingId = (int) $fileUploaderListElement['data']['listingFileId'];
                $sortForExisting[$listingId] = (int) $fileUploaderListElement['index'];
                continue;
            }
            if (!isset($fileUploaderListElement['data']['tmpFilePath'])) {
                continue;
            }

            $fileUploadDto = ListingFileUploadDto::fromFileUploaderListElement($fileUploaderListElement);
            if (!\file_exists($fileUploadDto->getSourceFilePath())) {
                continue;
            }
            FileHelper::throwExceptionIfUnsafePath($fileUploadDto->getSourceFilePath(), FilePath::getTempFileUpload());
            $tmpFile = new File($fileUploadDto->getSourceFilePath());
            $destinationPath = $this->getDestinationPath($listing, $fileUploadDto);
            FileHelper::throwExceptionIfUnsafePath($destinationPath, FilePath::getListingFilePath());
            $newFile = $tmpFile->move(\dirname($destinationPath), \basename($destinationPath));

            $listingFile = new ListingFile();
            $listingFile->setPath(Path::makeRelative($newFile->getRealPath(), FilePath::getPublicDir()));
            $listingFile->setFilename(\basename($listingFile->getPath()));
            $listingFile->setMimeType(\mime_content_type($listingFile->getPath()));
            $listingFile->setSizeBytes(\filesize($listingFile->getPath()));
            $listingFile->setSort($fileUploadDto->getSort());
            $this->em->persist($listingFile);

            $listing->addListingFile($listingFile);
        }

        foreach ($listing->getListingFiles() as $listingFile) {
            if (!isset($sortForExisting[$listingFile->getId()])) {
                continue;
            }
            $listingFile->setSort($sortForExisting[$listingFile->getId()]);
            $this->em->persist($listingFile);
        }

        $this->fileModificationEventService->updateListingMainImage($listing);
    }

    private function getDestinationPath(Listing $listing, ListingFileUploadDto $fileUploadDto): string
    {
        $destinationPath = $this->getDestinationDirectory($listing)
            . '/'
            . \basename($this->getDestinationFileName($fileUploadDto));


        FileHelper::throwExceptionIfUnsafePath($destinationPath, FilePath::getListingFilePath());

        return Path::canonicalize($destinationPath);
    }

    private function getDestinationDirectory(Listing $listing): string
    {
        $userDivider = \floor($listing->getUserNotNull()->getId() / 10000) + 1;

        return FilePath::getListingFilePath()
            . '/'
            . $userDivider
            . '/'
            . 'user_' . $listing->getUserNotNull()->getId()
            . '/'
            . 'listing_' . $listing->getId();
    }

    private function getDestinationFileName(ListingFileUploadDto $fileUploadDto): string
    {
        return FileHelper::getFilenameValidCharacters($fileUploadDto->getOriginalFilename())
        . '.'
        . \pathinfo($fileUploadDto->getOriginalFilename(), \PATHINFO_EXTENSION);
    }
}
