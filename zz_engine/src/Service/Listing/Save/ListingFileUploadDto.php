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

    public function __construct(string $path, string $originalFilename)
    {
        $this->path = $path;
        $this->originalFilename = $originalFilename;
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
}
