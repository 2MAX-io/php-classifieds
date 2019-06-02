<?php

declare(strict_types=1);

namespace App\Entity;

use App\Helper\ImageResizePath;
use App\Service\System\Sort\SortService;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ListingFileRepository")
 */
class ListingFile
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Listing", inversedBy="listingFiles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $listing;

    /**
     * @ORM\Column(type="smallint", nullable=false, options={"unsigned"=true})
     */
    private $sort = SortService::LAST_VALUE;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $path;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $filename;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $mimeType;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $sizeBytes;

    /**
     * @ORM\Column(type="boolean")
     */
    private $userRemoved = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getPathInListSize(): ?string
    {
        return ImageResizePath::forType(ImageResizePath::LIST, $this->getPath());
    }

    public function getPathInNormalSize(): ?string
    {
        return ImageResizePath::forType(ImageResizePath::NORMAL, $this->getPath());
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getListing(): ?Listing
    {
        return $this->listing;
    }

    public function setListing(?Listing $listing): self
    {
        $this->listing = $listing;

        return $this;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function getUserRemoved(): ?bool
    {
        return $this->userRemoved;
    }

    public function setUserRemoved(bool $userRemoved): self
    {
        $this->userRemoved = $userRemoved;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getSizeBytes(): ?int
    {
        return $this->sizeBytes;
    }

    public function setSizeBytes(int $sizeBytes): self
    {
        $this->sizeBytes = $sizeBytes;

        return $this;
    }
}
