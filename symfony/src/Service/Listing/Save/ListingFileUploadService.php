<?php

declare(strict_types=1);

namespace App\Service\Listing\Save;

use App\Entity\Listing;
use App\Entity\ListingFile;
use App\Helper\FileHelper;
use App\Helper\FilePath;
use App\Service\Event\FileModificationEventService;
use App\Service\System\Sort\SortService;
use Ausi\SlugGenerator\SlugGenerator;
use Ausi\SlugGenerator\SlugOptions;
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
            $destinationFilepath = $this->getDestinationFilepath($listing);
            $destinationFilename = $this->getDestinationFilename(
                $uploadedFile->getClientOriginalName(),
                $uploadedFile->getClientOriginalExtension()
            );
            $movedFile = $this->uploadFile(
                $uploadedFile,
                $destinationFilepath,
                \basename($destinationFilename)
            );
            $listingFile = new ListingFile();
            $listingFile->setPath(Path::makeRelative($movedFile->getRealPath(), FilePath::getProjectDir()));
            $listingFile->setListing($listing);
            $listingFile->setFilename(basename($listingFile->getPath()));
            $listingFile->setMimeType(mime_content_type($listingFile->getPath()));
            $listingFile->setSizeBytes(filesize($listingFile->getPath()));
            $listingFile->setSort(SortService::LAST_VALUE);
            $this->em->persist($listingFile);

            $listing->addListingFile($listingFile);
        }
    }

    public function updateSort(Listing $listing, array $fileUploaderList): void
    {
        $filenameIndex = [];
        foreach ($fileUploaderList as $fileUploaderListElement) {
            $fileName = basename($fileUploaderListElement['file']);
            $filenameIndex[$fileName] = $fileUploaderListElement['index'];
        }

        foreach ($listing->getListingFiles() as $listingFile) {
            $sort = 99999;
            if (isset($filenameIndex[basename($listingFile->getPath())])) {
                $sort = $filenameIndex[basename($listingFile->getPath())];
            }
            $listingFile->setSort((int) $sort);
            $this->em->persist($listingFile);
        }

        $this->fileModificationEventService->updateListingMainImage($listing);
    }

    public function uploadFile(UploadedFile $uploadedFile, string $destinationFilepath, string $destinationFilename): File
    {
        FileHelper::throwExceptionIfUnsafeExtension($uploadedFile);

        return $uploadedFile->move(
            $destinationFilepath,
            $destinationFilename
        );
    }

    private function getDestinationFilepath(Listing $listing): string
    {
        $userDivider = floor($listing->getUser()->getId() / 10000) + 1;

        return FilePath::getListingFilePath() . '/' . $userDivider . '/' . 'user_' . $listing->getUser()->getId() . '/' . 'listing_' . $listing->getId();
    }

    private function getDestinationFilename(string $originalName, string $originalExtension)
    {
        $fileBasename = $this->getSlug(
            pathinfo($originalName, PATHINFO_FILENAME)
        );

        return $fileBasename . '.' . $originalExtension;
    }

    private function getSlug(string $string): string
    {
        $generator = new SlugGenerator(
            (new SlugOptions)
                ->setValidChars('a-zA-Z0-9')
                ->setDelimiter('_')
        );

        return $generator->generate($string);
    }
}
