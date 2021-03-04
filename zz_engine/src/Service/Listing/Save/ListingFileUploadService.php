<?php

declare(strict_types=1);

namespace App\Service\Listing\Save;

use App\Entity\Listing;
use App\Entity\ListingFile;
use App\Helper\DateHelper;
use App\Helper\FileHelper;
use App\Helper\FilePath;
use App\Helper\JsonHelper;
use App\Helper\ListingFileHelper;
use App\Helper\RandomHelper;
use App\Service\Listing\Save\Dto\ListingFileUploadDto;
use App\Service\Listing\Save\Dto\ListingSaveDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\PathUtil\Path;

class ListingFileUploadService
{
    public const MAX_FILE_NAME_LENGTH = 70;
    public const FILEUPLOADER_FIELD_NAME_PREFIX = 'fileuploader-list-';
    public const UPLOADED_FILES_FIELD_NAME = 'files';
    public const FILEUPLOADER_FIELD_NAME = self::FILEUPLOADER_FIELD_NAME_PREFIX.self::UPLOADED_FILES_FIELD_NAME;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var OnListingFileModificationService
     */
    private $onListingFileModificationService;

    /**
     * @var Packages
     */
    private $packages;

    public function __construct(
        OnListingFileModificationService $onListingFileModificationService,
        Packages $packages,
        EntityManagerInterface $em
    ) {
        $this->em = $em;
        $this->onListingFileModificationService = $onListingFileModificationService;
        $this->packages = $packages;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getListingFilesForFrontend(ListingSaveDto $listingSaveDto): array
    {
        $files = $listingSaveDto->getUploadedFilesFromRequest();
        if (!empty($files)) {
            return \array_map(function ($file): array {
                if (isset($file['data']['tmpFilePath'])) {
                    $file['file'] = $this->packages->getUrl($file['data']['tmpFilePath']);
                }

                return $file;
            }, $files);
        }

        $returnFiles = [];
        foreach ($listingSaveDto->getListing()->getListingFiles() as $listingFile) {
            $returnFiles[] = [
                'name' => $listingFile->getUserOriginalFilename() ?? $listingFile->getFilename(),
                'type' => $listingFile->getMimeType(),
                'size' => $listingFile->getSizeBytes(),
                'file' => $this->packages->getUrl($listingFile->getPathInListSize()),
                'data' => [
                    'listingFileId' => $listingFile->getId(),
                    'filePath' => $this->packages->getUrl($listingFile->getPath()),
                ],
            ];
        }

        return $returnFiles;
    }

    public function saveUploadedFiles(ListingSaveDto $listingSaveDto): void
    {
        $listing = $listingSaveDto->getListing();
        $fileIdToSortPositionMap = [];
        foreach ($listingSaveDto->getUploadedFilesFromRequest() as $uploadedFileFromRequest) {
            if (isset($uploadedFileFromRequest['data']['listingFileId'])) {
                $listingId = (int) $uploadedFileFromRequest['data']['listingFileId'];
                $fileIdToSortPositionMap[$listingId] = (int) $uploadedFileFromRequest['index'];

                continue;
            }
            if (!isset($uploadedFileFromRequest['data']['tmpFilePath'])) {
                continue;
            }

            $fileUploadDto = ListingFileUploadDto::fromUploadedFileFromRequest($uploadedFileFromRequest);
            if (!\file_exists($fileUploadDto->getSourceFilePath())) {
                continue;
            }
            FileHelper::throwExceptionIfPathOutsideDir($fileUploadDto->getSourceFilePath(), FilePath::getTempFileUpload());
            $tmpFile = new File($fileUploadDto->getSourceFilePath());
            $destinationPath = $this->getDestinationPath($listing, $fileUploadDto);
            FileHelper::throwExceptionIfPathOutsideDir($destinationPath, FilePath::getListingFilePath());
            FileHelper::throwExceptionIfUnsafeFilename($destinationPath);
            $newFile = $tmpFile->move(\dirname($destinationPath), \basename($destinationPath));
            $newFilePath = $newFile->getRealPath();
            if (!$newFilePath) {
                throw new \RuntimeException("file not found, path: `{$newFile->getPath()}`, name: `{$newFile->getFilename()}`");
            }

            $imageWidth = null;
            $imageHeight = null;
            if (FileHelper::isImage($destinationPath)) {
                $imageSizeInfo = \getimagesize($destinationPath);
                if ($imageSizeInfo) {
                    [$imageWidth, $imageHeight] = $imageSizeInfo;
                }
            }

            $listingFile = new ListingFile();
            $listingFile->setPath(Path::makeRelative($newFilePath, FilePath::getPublicDir()));
            $listingFile->setFilename(\basename($listingFile->getPath()));
            $listingFile->setUserOriginalFilename(\mb_substr($fileUploadDto->getOriginalFilename(), 0, 255));
            $listingFile->setMimeType(\mime_content_type($listingFile->getPath()) ?: '');
            $listingFile->setSizeBytes(\filesize($listingFile->getPath()) ?: 0);
            $listingFile->setFileHash(\hash_file('sha256', $listingFile->getPath()) ?: '');
            $listingFile->setImageWidth($imageWidth);
            $listingFile->setImageHeight($imageHeight);
            $listingFile->setUploadDate(DateHelper::create());
            $listingFile->setSort($fileUploadDto->getSort());
            $this->em->persist($listingFile);

            $listing->addListingFile($listingFile);
        }

        foreach ($listing->getListingFiles() as $listingFile) {
            $sortPositionFound = isset($fileIdToSortPositionMap[$listingFile->getId()]);
            if (!$sortPositionFound) {
                continue;
            }
            $listingFile->setSort($fileIdToSortPositionMap[$listingFile->getId()]);
            $this->em->persist($listingFile);
        }

        $this->onListingFileModificationService->updateListingMainImage($listing);
    }

    /**
     * @return array<string,array|int|string>
     */
    public function getUploadedFilesFromRequest(Request $request): array
    {
        return JsonHelper::toArrayFromRequestKey($request, static::FILEUPLOADER_FIELD_NAME);
    }

    private function getDestinationPath(Listing $listing, ListingFileUploadDto $fileUploadDto): string
    {
        $destinationPath = ListingFileHelper::getDestinationDirectory($listing)
            .'/'
            .\basename($this->getDestinationFileName($fileUploadDto));

        FileHelper::throwExceptionIfPathOutsideDir($destinationPath, FilePath::getListingFilePath());
        FileHelper::throwExceptionIfUnsafeFilename($destinationPath);
        $destinationPath = Path::canonicalize($destinationPath);
        $destinationPath = FileHelper::reduceLengthOfFilenameOnly($destinationPath, self::MAX_FILE_NAME_LENGTH);

        return FileHelper::reduceLengthOfEntirePath($destinationPath);
    }

    private function getDestinationFileName(ListingFileUploadDto $fileUploadDto): string
    {
        $filenameValidCharacters = FileHelper::getFilenameValidCharacters($fileUploadDto->getOriginalFilename());
        $fileExtension = '.'.\pathinfo($fileUploadDto->getOriginalFilename(), \PATHINFO_EXTENSION);
        $fileExtensionLength = \strlen($fileExtension);
        $fileNameSuffix = '_'.RandomHelper::string(10);
        $fileNameSuffixLength = \strlen($fileNameSuffix);
        $fileNameWithReducedLength = \substr(
            $filenameValidCharacters,
            0,
            static::MAX_FILE_NAME_LENGTH - $fileNameSuffixLength - $fileExtensionLength
        );
        $fileNameWithReducedLength = \trim($fileNameWithReducedLength, '_');

        $destinationFilename = \basename($fileNameWithReducedLength.$fileNameSuffix.$fileExtension);
        FileHelper::throwExceptionIfUnsafeFilename($destinationFilename);

        return $destinationFilename;
    }
}
