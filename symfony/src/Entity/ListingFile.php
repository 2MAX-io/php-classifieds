<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\PathUtil\Path;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ListingFileRepository")
 */
class ListingFile
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Listing", inversedBy="listingFiles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $listing;

    /**
     * @ORM\Column(type="smallint", nullable=false)
     */
    private $sort = 999;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $path;

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

    public function getPathListSize(): ?string
    {
        return $this->getSizeForType('list');
    }

    public function getPathNormalSize(): ?string
    {
        return $this->getSizeForType('normal');
    }

    public function getSizeForType(string $type): string
    {
        return Path::getDirectory($this->getPath()) . '/size_' . basename($type) . '_' . basename($this->getPath());
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
}
