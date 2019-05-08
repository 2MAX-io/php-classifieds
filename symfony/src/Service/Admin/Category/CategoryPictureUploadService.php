<?php

declare(strict_types=1);

namespace App\Service\Admin\Category;

use App\Entity\Category;
use App\Helper\File;
use App\Helper\FilePath;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Webmozart\PathUtil\Path;

class CategoryPictureUploadService
{
    public function savePicture(Category $category, UploadedFile $uploadedFile): void
    {
        File::throwExceptionIfUnsafeExtension($uploadedFile);

        $destinationFilename = $this->getDestinationFilename($uploadedFile);

        if (!File::isImage($destinationFilename)) {
            throw new \UnexpectedValueException(
                "file is not image"
            );
        }

        $movedFile = $uploadedFile->move(
            FilePath::getCategoryPicturePath(),
            \basename($destinationFilename)
        );

        $category->setPicture(Path::makeRelative($movedFile->getRealPath(), FilePath::getProjectDir()));
    }

    private function getDestinationFilename(UploadedFile $uploadedFile): string
    {
        $ext = $uploadedFile->getClientOriginalExtension();

        return File::getFilenameValidCharacters($uploadedFile->getClientOriginalName()) . '.' . $ext;
    }
}
