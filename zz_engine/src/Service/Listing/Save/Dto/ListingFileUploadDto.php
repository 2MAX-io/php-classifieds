<?php

declare(strict_types=1);

namespace App\Service\Listing\Save\Dto;

use App\Helper\FileHelper;
use App\Helper\FilePath;

class ListingFileUploadDto
{
    /**
     * @var string
     */
    private $sourcePath;

    /**
     * @var string
     */
    private $originalFilename;

    /**
     * @var int
     */
    private $sort;

    public function __construct(string $path, string $originalFilename, int $sort)
    {
        $this->sourcePath = $path;
        $this->originalFilename = $originalFilename;
        $this->sort = $sort;
    }

    /**
     * @param array<string,mixed> $fileUploaderListElement
     */
    public static function fromUploadedFileFromRequest(array $fileUploaderListElement): self
    {
        $sourceFilePath = FilePath::getPublicDir().'/'.$fileUploaderListElement['data']['tmpFilePath'];
        FileHelper::throwExceptionIfPathOutsideDir($sourceFilePath, FilePath::getTempFileUpload());
        FileHelper::throwExceptionIfUnsafeFilename($fileUploaderListElement['name']);

        // remove query string
        $originalFilename = \preg_replace('~(\?.+)$~', '', \basename($fileUploaderListElement['name']));

        return new ListingFileUploadDto(
            $sourceFilePath,
            $originalFilename,
            (int) $fileUploaderListElement['index']
        );
    }

    public function getSourceFilePath(): string
    {
        return $this->sourcePath;
    }

    public function setSourcePath(string $sourcePath): void
    {
        $this->sourcePath = $sourcePath;
    }

    public function getOriginalFilename(): string
    {
        return $this->originalFilename;
    }

    public function setOriginalFilename(string $originalFilename): void
    {
        $this->originalFilename = $originalFilename;
    }

    public function getSort(): int
    {
        return $this->sort;
    }

    public function setSort(int $sort): void
    {
        $this->sort = $sort;
    }
}
