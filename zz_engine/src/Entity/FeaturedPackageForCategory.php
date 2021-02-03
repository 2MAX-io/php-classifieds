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
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=false)
     */
    private $id;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="featuredPackages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @var FeaturedPackage
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\FeaturedPackage", inversedBy="featuredPackageForCategories")
     * @ORM\JoinColumn(nullable=false)
     */
    private $featuredPackage;

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
