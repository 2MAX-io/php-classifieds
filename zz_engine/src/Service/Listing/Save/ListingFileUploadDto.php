<?php

declare(strict_types=1);

namespace App\Service\Listing\Save;

class ListingFileUploadDto
{
    /**
     * @var string
     */
    private $path;

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
        $this->path = $path;
        $this->originalFilename = $originalFilename;
        $this->sort = $sort;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
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
