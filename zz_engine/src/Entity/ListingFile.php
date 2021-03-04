<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\SortConfig;
use App\Helper\ResizedImagePath;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class ListingFile
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=false)
     */
    private $id;

    /**
     * @var Listing
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Listing", inversedBy="listingFiles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $listing;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $filename;

    /**
     * @var null|string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $userOriginalFilename;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $mimeType;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $sizeBytes;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $fileHash;

    /**
     * @var null|int
     *
     * @ORM\Column(type="smallint", nullable=true, options={"unsigned"=true})
     */
    private $imageWidth;

    /**
     * @var null|int
     *
     * @ORM\Column(type="smallint", nullable=true, options={"unsigned"=true})
     */
    private $imageHeight;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $userRemoved = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $fileDeleted = false;

    /**
     * @var null|\DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $uploadDate;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=false, options={"unsigned"=true})
     */
    private $sort = SortConfig::LAST_VALUE;

    public function getPathInListSize(): ?string
    {
        return ResizedImagePath::forType(ResizedImagePath::LIST, $this->getPath());
    }

    public function getPathInNormalSize(): ?string
    {
        return ResizedImagePath::forType(ResizedImagePath::NORMAL, $this->getPath());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): string
    {
        return $this->path;
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

    public function getListingNotNull(): Listing
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

    public function getUserOriginalFilename(): ?string
    {
        return $this->userOriginalFilename;
    }

    public function setUserOriginalFilename(?string $userOriginalFilename): void
    {
        $this->userOriginalFilename = $userOriginalFilename;
    }

    public function getFileHash(): string
    {
        return $this->fileHash;
    }

    public function setFileHash(string $fileHash): void
    {
        $this->fileHash = $fileHash;
    }

    public function getFileDeleted(): bool
    {
        return $this->fileDeleted;
    }

    public function setFileDeleted(bool $fileDeleted): void
    {
        $this->fileDeleted = $fileDeleted;
    }

    public function getUploadDate(): ?\DateTimeInterface
    {
        return $this->uploadDate;
    }

    public function setUploadDate(\DateTimeInterface $uploadDate): void
    {
        $this->uploadDate = $uploadDate;
    }

    public function getImageWidth(): ?int
    {
        return $this->imageWidth;
    }

    public function setImageWidth(?int $imageWidth): void
    {
        $this->imageWidth = $imageWidth;
    }

    public function getImageHeight(): ?int
    {
        return $this->imageHeight;
    }

    public function setImageHeight(?int $imageHeight): void
    {
        $this->imageHeight = $imageHeight;
    }
}
