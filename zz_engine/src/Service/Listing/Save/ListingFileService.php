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
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

            $fileUploadDto = new ListingFileUploadDto(
                $fileUploaderListElement['tmpFilePath'],
                \preg_replace('#(\?.+)$#', '', \basename($fileUploaderListElement['name'])),
                (int) $fileUploaderListElement['index']
                );
            $filePath = FilePath::getPublicDir() . '/' . $fileUploadDto->getPath();
            if (!\file_exists($filePath)) {
                continue;
            }
            $tmpFile = new File($filePath);
            FileHelper::throwExceptionIfUnsafeExtension($tmpFile->getExtension());
            $destinationFileName = FileHelper::getFilenameValidCharacters($fileUploadDto->getOriginalFilename())
                . '.'
                . $tmpFile->getExtension();
            $destinationDir = $this->getDestinationDirectory($listing);
            $newFile = $tmpFile->move($destinationDir, $destinationFileName);

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
