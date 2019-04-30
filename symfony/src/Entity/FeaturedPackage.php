<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FeaturedPackageRepository")
 */
class FeaturedPackage
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $adminName;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $price;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $daysFeaturedExpire;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $daysListingExpire;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $defaultPackage;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FeaturedPackageForCategory", mappedBy="featuredPackage")
     */
    private $featuredPackageForCategories;

    public function __construct()
    {
        $this->featuredPackageForCategories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdminName(): ?string
    {
        return $this->adminName;
    }

    public function setAdminName(string $adminName): self
    {
        $this->adminName = $adminName;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDaysFeaturedExpire(): ?int
    {
        return $this->daysFeaturedExpire;
    }

    public function setDaysFeaturedExpire(int $daysFeaturedExpire): self
    {
        $this->daysFeaturedExpire = $daysFeaturedExpire;

        return $this;
    }

    public function getDaysListingExpire(): ?int
    {
        return $this->daysListingExpire;
    }

    public function setDaysListingExpire(int $daysListingExpire): self
    {
        $this->daysListingExpire = $daysListingExpire;

        return $this;
    }

    public function getDefaultPackage(): ?bool
    {
        return $this->defaultPackage;
    }

    public function setDefaultPackage(bool $defaultPackage): self
    {
        $this->defaultPackage = $defaultPackage;

        return $this;
    }

    /**
     * @return Collection|FeaturedPackageForCategory[]
     */
    public function getFeaturedPackageForCategories(): Collection
    {
        return $this->featuredPackageForCategories;
    }

    public function addFeaturedPackageForCategory(FeaturedPackageForCategory $featuredPackageForCategory): self
    {
        if (!$this->featuredPackageForCategories->contains($featuredPackageForCategory)) {
            $this->featuredPackageForCategories[] = $featuredPackageForCategory;
            $featuredPackageForCategory->setFeaturedPackage($this);
        }

        return $this;
    }

    public function removeFeaturedPackageForCategory(FeaturedPackageForCategory $featuredPackageForCategory): self
    {
        if ($this->featuredPackageForCategories->contains($featuredPackageForCategory)) {
            $this->featuredPackageForCategories->removeElement($featuredPackageForCategory);
            // set the owning side to null (unless already changed)
            if ($featuredPackageForCategory->getFeaturedPackage() === $this) {
                $featuredPackageForCategory->setFeaturedPackage(null);
            }
        }

        return $this;
    }
}
