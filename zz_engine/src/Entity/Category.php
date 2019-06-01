<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 * @ORM\Table(indexes={
 *     @Index(columns={"lft", "rgt"}, name="lft_rgt"),
 * })
 * @UniqueEntity(fields={"slug"})
 */
class Category
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
    private $name;

    /**
     * @ORM\Column(type="string", length=100, unique=true, nullable=false)
     */
    private $slug;

    /**
     * @ORM\Column(type="smallint", nullable=false, options={"unsigned"=true})
     */
    private $sort;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="children")
     */
    private $parent;

    /**
     * @ORM\Column(name="lft", type="integer", nullable=false)
     */
    private $lft;

    /**
     * @ORM\Column(name="rgt", type="integer", nullable=false)
     */
    private $rgt;

    /**
     * @ORM\Column(name="lvl", type="integer", nullable=false)
     */
    private $lvl;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Category", mappedBy="parent")
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    private $children;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Listing", mappedBy="category")
     */
    private $listings;

    /**
     * @var CustomFieldJoinCategory[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\CustomFieldJoinCategory", mappedBy="category")
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    private $customFieldsJoin;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $picture;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FeaturedPackageForCategory", mappedBy="category")
     */
    private $featuredPackages;

    public function __construct()
    {
        $this->listings = new ArrayCollection();
        $this->customFieldsJoin = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->featuredPackages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Listing[]
     */
    public function getListings(): Collection
    {
        return $this->listings;
    }

    public function addListing(Listing $listing): self
    {
        if (!$this->listings->contains($listing)) {
            $this->listings[] = $listing;
            $listing->setCategory($this);
        }

        return $this;
    }

    public function removeListing(Listing $listing): self
    {
        if ($this->listings->contains($listing)) {
            $this->listings->removeElement($listing);
            // set the owning side to null (unless already changed)
            if ($listing->getCategory() === $this) {
                $listing->setCategory(null);
            }
        }

        return $this;
    }

    public function getLft(): ?int
    {
        return $this->lft;
    }

    public function setLft(int $lft): self
    {
        $this->lft = $lft;

        return $this;
    }

    public function getRgt(): ?int
    {
        return $this->rgt;
    }

    public function setRgt(int $rgt): self
    {
        $this->rgt = $rgt;

        return $this;
    }

    public function getLvl(): ?int
    {
        return $this->lvl;
    }

    public function setLvl(int $lvl): self
    {
        $this->lvl = $lvl;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * make sure hydrator do not have make additional queries when using this
     *
     * @return Category[]
     */
    public function getPath(): array
    {
        $path = [];

        $category = $this;
        while ($category->getParent()) {
            $path[] = $category;
            $category = $category->getParent();
        }

        return array_reverse($path);
    }

    /**
     * @return Collection|Category[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChildren(Category $children): self
    {
        if (!$this->children->contains($children)) {
            $this->children[] = $children;
            $children->setParent($this);
        }

        return $this;
    }

    public function removeChildren(Category $children): self
    {
        if ($this->children->contains($children)) {
            $this->children->removeElement($children);
            // set the owning side to null (unless already changed)
            if ($children->getParent() === $this) {
                $children->setParent(null);
            }
        }

        return $this;
    }

    public function addChild(Category $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(Category $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

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

    /**
     * @return Collection|FeaturedPackageForCategory[]
     */
    public function getFeaturedPackages(): Collection
    {
        return $this->featuredPackages;
    }

    public function addFeaturedPackage(FeaturedPackageForCategory $featuredPackage): self
    {
        if (!$this->featuredPackages->contains($featuredPackage)) {
            $this->featuredPackages[] = $featuredPackage;
            $featuredPackage->setCategory($this);
        }

        return $this;
    }

    public function removeFeaturedPackage(FeaturedPackageForCategory $featuredPackage): self
    {
        if ($this->featuredPackages->contains($featuredPackage)) {
            $this->featuredPackages->removeElement($featuredPackage);
            // set the owning side to null (unless already changed)
            if ($featuredPackage->getCategory() === $this) {
                $featuredPackage->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CustomField[]
     */
    public function getCustomFields(): Collection
    {
        return $this->getCustomFieldsJoin()->map(function(CustomFieldJoinCategory $el) {
            return $el->getCustomField();
        });
    }

    /**
     * @return Collection|CustomFieldJoinCategory[]
     */
    public function getCustomFieldsJoin(): Collection
    {
        return $this->customFieldsJoin;
    }

    public function addCustomFieldsJoin(CustomFieldJoinCategory $customFieldsJoin): self
    {
        if (!$this->customFieldsJoin->contains($customFieldsJoin)) {
            $this->customFieldsJoin[] = $customFieldsJoin;
            $customFieldsJoin->setCategory($this);
        }

        return $this;
    }

    public function removeCustomFieldsJoin(CustomFieldJoinCategory $customFieldsJoin): self
    {
        if ($this->customFieldsJoin->contains($customFieldsJoin)) {
            $this->customFieldsJoin->removeElement($customFieldsJoin);
            // set the owning side to null (unless already changed)
            if ($customFieldsJoin->getCategory() === $this) {
                $customFieldsJoin->setCategory(null);
            }
        }

        return $this;
    }
}
