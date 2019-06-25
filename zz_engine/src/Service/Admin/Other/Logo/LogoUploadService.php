<?php

declare(strict_types=1);

namespace App\Service\Admin\Other\Logo;

use App\Helper\FileHelper;
use App\Helper\FilePath;
use App\Service\Setting\SettingsService;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Webmozart\PathUtil\Path;

class LogoUploadService
{
    /**
     * @var SettingsService
     */
    private $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
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

        $this->saveLogoSetting(Path::makeRelative($movedFile->getRealPath(), FilePath::getProjectDir()));
    }

    public function getLogoPath(): ?string
    {
        return $this->settingsService->getSettingsDtoWithoutCache()->getLogoPath();
    }

    private function getDestinationPath(UploadedFile $uploadedFile): string
    {
        $extension = $uploadedFile->getClientOriginalExtension();

        return FilePath::getLogoPath()
            . '/' . FileHelper::getFilenameValidCharacters($uploadedFile->getClientOriginalName()) . '.' . $extension;
    }

    private function saveLogoSetting(string $logoPath): void
    {
        $settingsDto = $this->settingsService->getSettingsDtoWithoutCache();
        $settingsDto->setLogoPath($logoPath);
        $this->settingsService->save($settingsDto);
    }
}
