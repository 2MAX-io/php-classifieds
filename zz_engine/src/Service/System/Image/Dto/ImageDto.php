<?php

declare(strict_types=1);

namespace App\Service\System\Image\Dto;

class ImageDto
{
    /**
     * @var string
     */
    private $sourcePath;

    /**
     * @var string
     */
    private $type;

    /**
     * @var int|null
     */
    private $width;

    /**
     * @var int|null
     */
    private $height;

    /**
     * @var array
     */
    private $imageParams = [];

    public function updateSize(): void
    {
        if (!\file_exists($this->getSourcePath())) {
            throw new \RuntimeException("file not found in path: {$this->getSourcePath()}");
        }

        [$this->width, $this->height] = \getimagesize($this->getSourcePath());
    }

    public function getSourcePath(): string
    {
        return $this->sourcePath;
    }

    public function setSourcePath(string $sourcePath): void
    {
        $this->sourcePath = $sourcePath;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(?int $width): void
    {
        $this->width = $width;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): void
    {
        $this->height = $height;
    }

    public function getImageParams(): array
    {
        return $this->imageParams;
    }

    public function setImageParams(array $imageParams): void
    {
        $this->imageParams = $imageParams;
    }
}
