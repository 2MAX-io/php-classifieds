<?php

declare(strict_types=1);

namespace App\Service\Admin\Category;

use App\Entity\Category;
use App\Helper\FileHelper;
use App\Helper\FilePath;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Webmozart\PathUtil\Path;

class CategoryPictureUploadService
{
    public function savePicture(Category $category, UploadedFile $uploadedFile): void
    {
        FileHelper::throwExceptionIfUnsafeExtensionFromUploadedFile($uploadedFile);
        $destinationPath = $this->getDestinationPath($uploadedFile);
        FileHelper::throwExceptionIfNotImage($destinationPath);
        FileHelper::throwExceptionIfPathOutsideDir($destinationPath, FilePath::getCategoryPicturePath());

        $movedFile = $uploadedFile->move(
            \dirname($destinationPath),
            \basename($destinationPath)
        );

        $category->setPicture(Path::makeRelative($movedFile->getRealPath(), FilePath::getProjectDir()));
    }

    private function getDestinationPath(UploadedFile $uploadedFile): string
    {
        $extension = $uploadedFile->getClientOriginalExtension();

        $return = FilePath::getCategoryPicturePath()
            . '/'
            . FileHelper::getFilenameValidCharacters($uploadedFile->getClientOriginalName()) . '.' . $extension;
        $return = FileHelper::reduceFilenameLength($return, 50);
        $return = FileHelper::reducePathLength($return, 100);

        return $return;
    }
}
