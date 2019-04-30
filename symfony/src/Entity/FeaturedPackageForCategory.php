<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class FeaturedPackageForCategory
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FeaturedPackage", inversedBy="featuredPackageForCategories")
     */
    private $featuredPackage;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="featuredPackages")
     */
    private $category;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFeaturedPackage(): ?FeaturedPackage
    {
        return $this->featuredPackage;
    }

    public function setFeaturedPackage(?FeaturedPackage $featuredPackage): self
    {
        $this->featuredPackage = $featuredPackage;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
}
