<?php

declare(strict_types=1);

namespace App\Service\Listing\Save;

use App\Entity\Listing;
use App\Entity\ListingFile;
use App\Helper\DateHelper;
use App\Helper\FileHelper;
use App\Helper\FilePath;
use App\Helper\ListingFileHelper;
use App\Helper\Random;
use App\Service\Event\FileModificationEventService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Webmozart\PathUtil\Path;

class ListingFileUploadService
{
    public const MAX_FILE_NAME_LENGTH = 70;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var FileModificationEventService
     */
    private $fileModificationEventService;

    public function __construct(EntityManagerInterface $em, FileModificationEventService $fileModificationEventService)
    {
        $this->em = $em;
        $this->fileModificationEventService = $fileModificationEventService;
    }

    public function processListingFiles(Listing $listing, array $fileUploaderList): void
    {
        $fileIdToSortIndexMap = [];
        foreach ($fileUploaderList as $fileUploaderListElement) {
            if (isset($fileUploaderListElement['data']['listingFileId'])) {
                $listingId = (int) $fileUploaderListElement['data']['listingFileId'];
                $fileIdToSortIndexMap[$listingId] = (int) $fileUploaderListElement['index'];
                continue;
            }
            if (!isset($fileUploaderListElement['data']['tmpFilePath'])) {
                continue;
            }

            $fileUploadDto = ListingFileUploadDto::fromFileUploaderListElement($fileUploaderListElement);
            if (!\file_exists($fileUploadDto->getSourceFilePath())) {
                continue;
            }
            FileHelper::throwExceptionIfPathOutsideDir($fileUploadDto->getSourceFilePath(), FilePath::getTempFileUpload());
            $tmpFile = new File($fileUploadDto->getSourceFilePath());
            $destinationPath = $this->getDestinationPath($listing, $fileUploadDto);
            FileHelper::throwExceptionIfPathOutsideDir($destinationPath, FilePath::getListingFilePath());
            FileHelper::throwExceptionIfUnsafeFilename($destinationPath);
            $newFile = $tmpFile->move(\dirname($destinationPath), \basename($destinationPath));

            $listingFile = new ListingFile();
            $listingFile->setPath(Path::makeRelative($newFile->getRealPath(), FilePath::getPublicDir()));
            $listingFile->setFilename(\basename($listingFile->getPath()));
            $listingFile->setMimeType(\mime_content_type($listingFile->getPath()));
            $listingFile->setSizeBytes(\filesize($listingFile->getPath()));
            $listingFile->setFileHash(\hash_file('sha256', $listingFile->getPath()));
            $listingFile->setUploadDate(DateHelper::create());
            $listingFile->setUserOriginalFilename(\mb_substr($fileUploadDto->getOriginalFilename(), 0, 255));
            $listingFile->setSort($fileUploadDto->getSort());
            $this->em->persist($listingFile);

            $listing->addListingFile($listingFile);
        }

        foreach ($listing->getListingFiles() as $listingFile) {
            if (!isset($fileIdToSortIndexMap[$listingFile->getId()])) {
                continue;
            }
            $listingFile->setSort($fileIdToSortIndexMap[$listingFile->getId()]);
            $this->em->persist($listingFile);
        }

        $this->fileModificationEventService->updateListingMainImage($listing);
    }

    private function getDestinationPath(Listing $listing, ListingFileUploadDto $fileUploadDto): string
    {
        $destinationPath = ListingFileHelper::getDestinationDirectory($listing)
            . '/'
            . \basename($this->getDestinationFileName($fileUploadDto));


        FileHelper::throwExceptionIfPathOutsideDir($destinationPath, FilePath::getListingFilePath());
        $destinationPath = Path::canonicalize($destinationPath);
        $destinationPath = FileHelper::reduceFilenameLength($destinationPath, self::MAX_FILE_NAME_LENGTH);
        $destinationPath = FileHelper::reducePathLength($destinationPath);

        return $destinationPath;
    }

    private function getDestinationFileName(ListingFileUploadDto $fileUploadDto): string
    {
        $filenameValidCharacters = FileHelper::getFilenameValidCharacters($fileUploadDto->getOriginalFilename());
        $extensionSuffix = '.' . \pathinfo($fileUploadDto->getOriginalFilename(), \PATHINFO_EXTENSION);
        $extensionSuffixLength = \strlen($extensionSuffix);
        $fileNameSuffix = '_' . Random::string(10);
        $fileNameSuffixLength = \strlen($fileNameSuffix);
        $fileNameWithReducedLength = \substr(
            $filenameValidCharacters,
            0,
            static::MAX_FILE_NAME_LENGTH - $fileNameSuffixLength - $extensionSuffixLength
        );
        $fileNameWithReducedLength = \trim($fileNameWithReducedLength, '_');

        return \basename($fileNameWithReducedLength . $fileNameSuffix . $extensionSuffix);
    }
}
