<?php

declare(strict_types=1);

namespace App\Service\Admin\Other\Logo;

use App\Helper\FileHelper;
use App\Helper\FilePath;
use App\Service\Setting\SettingsSaveService;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Webmozart\PathUtil\Path;

class LogoUploadService
{
    /**
     * @var SettingsSaveService
     */
    private $settingsSaveService;

    public function __construct(SettingsSaveService $settingsSaveService)
    {
        $this->settingsSaveService = $settingsSaveService;
    }

    public function saveLogo(UploadedFile $uploadedFile): void
    {
        FileHelper::throwExceptionIfUnsafeExtensionFromUploadedFile($uploadedFile);

        $destinationPath = $this->getDestinationPath($uploadedFile);
        FileHelper::throwExceptionIfNotImage($destinationPath);
        FileHelper::throwExceptionIfUnsafeFilename($destinationPath);
        FileHelper::throwExceptionIfPathOutsideDir($destinationPath, FilePath::getLogoPath());
        $movedFile = $uploadedFile->move(
            \dirname($destinationPath),
            \basename($destinationPath)
        );
        $movedFilePath = $movedFile->getRealPath();
        if (!$movedFilePath) {
            throw new \RuntimeException("file not found, path: `{$movedFile->getPath()}`, name: `{$movedFile->getFilename()}`");
        }

        $this->saveLogoSetting(Path::makeRelative($movedFilePath, FilePath::getPublicDir()));
    }

    public function getLogoPath(): ?string
    {
        return $this->settingsSaveService->getSettingsDtoWithoutCache()->getLogoPath();
    }

    private function getDestinationPath(UploadedFile $uploadedFile): string
    {
        $extension = $uploadedFile->getClientOriginalExtension();
        $destinationPath = FilePath::getLogoPath()
            .'/'.FileHelper::getFilenameValidCharacters($uploadedFile->getClientOriginalName())
            .'.'.$extension;
        $destinationPath = FileHelper::reduceLengthOfFilenameOnly($destinationPath, 50);

        return FileHelper::reduceLengthOfEntirePath($destinationPath, 100);
    }

    private function saveLogoSetting(string $logoPath): void
    {
        $settingsDto = $this->settingsSaveService->getSettingsDtoWithoutCache();
        $settingsDto->setLogoPath($logoPath);
        $this->settingsSaveService->save($settingsDto);
    }
}
