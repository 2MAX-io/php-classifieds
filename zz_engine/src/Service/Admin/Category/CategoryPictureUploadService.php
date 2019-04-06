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
            \basename($destinationPath),
        );
        $movedFilePath = $movedFile->getRealPath();
        if (!$movedFilePath) {
            throw new \RuntimeException("file not found, path: `{$movedFile->getPath()}`, name: `{$movedFile->getFilename()}`");
        }

        $category->setPicture(Path::makeRelative($movedFilePath, FilePath::getPublicDir()));
    }

    private function getDestinationPath(UploadedFile $uploadedFile): string
    {
        $extension = $uploadedFile->getClientOriginalExtension();

        $return = FilePath::getCategoryPicturePath()
            .'/'.FileHelper::getFilenameValidCharacters($uploadedFile->getClientOriginalName()).'.'.$extension;
        $return = FileHelper::reduceLengthOfFilenameOnly($return, 50);

        return FileHelper::reduceLengthOfEntirePath($return, 100);
    }
}
