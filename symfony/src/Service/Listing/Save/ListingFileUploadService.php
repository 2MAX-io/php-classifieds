<?php

declare(strict_types=1);

namespace App\Service\Listing\Save;

use App\Entity\Listing;
use App\Entity\ListingFile;
use App\Helper\Arr;
use App\Helper\FilePath;
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

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    public function addBannerFileFromUpload(Listing $listing, UploadedFile $uploadedFile): void
    {
        $destinationFilepath = $this->getDestinationFilepath($listing);
        $destinationFilename = $this->getDestinationFilename(
            $uploadedFile->getClientOriginalName(),
            $uploadedFile->getClientOriginalExtension()
        );
        $movedFile = $this->uploadBannerFile(
            $uploadedFile,
            $destinationFilepath,
            $destinationFilename
        );
        $listingFile = new ListingFile();
        $listingFile->setPath(Path::makeRelative($movedFile->getRealPath(), FilePath::getProjectDir()));
        $listingFile->setListing($listing);
        $this->em->persist($listingFile);

        $listing->addListingFile($listingFile);
    }

    public function uploadBannerFile(UploadedFile $uploadedFile, string $destinationFilepath, string $destinationFilename): File
    {
        $this->throwExceptionIfUnsafeExtension($uploadedFile);

        return $uploadedFile->move(
            $destinationFilepath,
            $destinationFilename
        );
    }

    private function throwExceptionIfUnsafeExtension(UploadedFile $uploadedFile): void
    {
        $fileExtension = $uploadedFile->getClientOriginalExtension();
        if (!Arr::inArray(
            $fileExtension,
            [
                'jpg',
                'jpeg',
                'png',
                'gif',
                'swf',
            ]
        )) {
            throw new \UnexpectedValueException(
                "file extension $fileExtension is not allowed"
            ); // todo: #11 better passing exception to user
        }

        if (Arr::inArray(
            $fileExtension,
            [
                'php',
                'js',
                'css',
                'exe',
                'com',
                'bat',
                'sh',
                'cgi',
            ]
        )) {
            throw new \UnexpectedValueException(
                "file extension $fileExtension is not allowed"
            );// todo: #11 better passing exception to user
        }
    }

    private function getDestinationFilepath(Listing $listing): string
    {
        return FilePath::getListingFilePath() . '/' . $this->getSlug('zzzz') . '/' . date('Y');
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
