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
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=false)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=70, nullable=false)
     */
    private $adminName;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $defaultPackage;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $price;

    /**
     * @ORM\Column(type="smallint", nullable=false, options={"unsigned"=true})
     */
    private $daysFeaturedExpire;

    /**
     * @ORM\Column(type="smallint", nullable=false, options={"unsigned"=true})
     */
    private $daysListingExpire;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $removed = false;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10000, nullable=true)
     */
    private $description;

    /**
     * @var FeaturedPackageForCategory[]|Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\FeaturedPackageForCategory", mappedBy="featuredPackage")
     */
    private $featuredPackageForCategories;

    /**
     * @var PaymentForFeaturedPackage[]|Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\PaymentForFeaturedPackage", mappedBy="featuredPackage")
     */
    private $paymentFeaturedPackage;

    /**
     * @var float
     *
     * used only for form auto validation, if saving empty featured package form successful can be removed
     */
    private /** @noinspection PhpUnusedPrivateFieldInspection */ $priceFloat;

    public function __construct()
    {
        $this->featuredPackageForCategories = new ArrayCollection();
        $this->paymentFeaturedPackage = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdminName(): ?string
    {
        return $this->adminName;
    }

    public function setAdminName(?string $adminName): self
    {
        $this->adminName = $adminName;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPriceFloat(): ?float
    {
        return $this->price / 100;
    }

    public function setPriceFloat(?float $price): self
    {
        $this->price = (int) ($price * 100);

        return $this;
    }

    public function getDaysFeaturedExpire(): ?int
    {
        return $this->daysFeaturedExpire;
    }

    public function setDaysFeaturedExpire(?int $daysFeaturedExpire): self
    {
        $this->daysFeaturedExpire = $daysFeaturedExpire;

        return $this;
    }

    public function getDaysListingExpire(): ?int
    {
        return $this->daysListingExpire;
    }

    public function setDaysListingExpire(?int $daysListingExpire): self
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

    /**
     * @return Collection|PaymentForFeaturedPackage[]
     */
    public function getPaymentFeaturedPackage(): Collection
    {
        return $this->paymentFeaturedPackage;
    }

    public function addPaymentFeaturedPackage(PaymentForFeaturedPackage $paymentFeaturedPackage): self
    {
        if (!$this->paymentFeaturedPackage->contains($paymentFeaturedPackage)) {
            $this->paymentFeaturedPackage[] = $paymentFeaturedPackage;
            $paymentFeaturedPackage->setFeaturedPackage($this);
        }

        return $this;
    }

    public function removePaymentFeaturedPackage(PaymentForFeaturedPackage $paymentFeaturedPackage): self
    {
        if ($this->paymentFeaturedPackage->contains($paymentFeaturedPackage)) {
            $this->paymentFeaturedPackage->removeElement($paymentFeaturedPackage);
            // set the owning side to null (unless already changed)
            if ($paymentFeaturedPackage->getFeaturedPackage() === $this) {
                $paymentFeaturedPackage->setFeaturedPackage(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRemoved(): ?bool
    {
        return $this->removed;
    }

    public function setRemoved(bool $removed): self
    {
        $this->removed = $removed;

        return $this;
    }
}
