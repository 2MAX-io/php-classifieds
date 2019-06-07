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

        $destinationFilename = $this->getDestinationFilename($uploadedFile);

        if (!FileHelper::isImage($destinationFilename)) {
            throw new \UnexpectedValueException(
                'file is not image'
            );
        }

        $movedFile = $uploadedFile->move(
            FilePath::getLogoPath(),
            \basename($destinationFilename)
        );

        $this->saveLogoSetting(Path::makeRelative($movedFile->getRealPath(), FilePath::getProjectDir()));
    }

    public function getLogoPath(): ?string
    {
        return $this->settingsService->getSettingsDtoWithoutCache()->getLogoPath();
    }

    private function getDestinationFilename(UploadedFile $uploadedFile): string
    {
        $ext = $uploadedFile->getClientOriginalExtension();

        return FileHelper::getFilenameValidCharacters($uploadedFile->getClientOriginalName()) . '.' . $ext;
    }

    private function saveLogoSetting(string $logoPath): void
    {
        $settingsDto = $this->settingsService->getSettingsDtoWithoutCache();
        $settingsDto->setLogoPath($logoPath);
        $this->settingsService->save($settingsDto);
    }
}
