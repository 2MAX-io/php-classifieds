<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PackageRepository")
 */
class Package
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
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=false, options={"unsigned"=true})
     */
    private $daysFeaturedExpire;

    /**
     * @var int
     *
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
     * @var string|null
     *
     * @ORM\Column(type="string", length=10000, nullable=true)
     */
    private $description;

    /**
     * @var Collection|PackageForCategory[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\PackageForCategory", mappedBy="package")
     */
    private $packageForCategories;

    /**
     * @var Collection|PaymentForPackage[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\PaymentForPackage", mappedBy="package")
     */
    private $paymentForPackage;

    /**
     * @var float|null
     *
     * used only for form auto validation, if saving empty package form successful can be removed
     */
    private $priceFloat;

    public function __construct()
    {
        $this->packageForCategories = new ArrayCollection();
        $this->paymentForPackage = new ArrayCollection();
    }

    public function getPriceFloat(): ?float
    {
        return $this->price / 100;
    }

    public function setPriceFloat(?float $price): self
    {
        $this->priceFloat = $price;
        $this->price = (int) ($price * 100);

        return $this;
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
     * @return Collection|PackageForCategory[]
     */
    public function getPackageForCategories(): Collection
    {
        return $this->packageForCategories;
    }

    public function addPackageForCategory(PackageForCategory $packageForCategory): self
    {
        if (!$this->packageForCategories->contains($packageForCategory)) {
            $this->packageForCategories[] = $packageForCategory;
            $packageForCategory->setPackage($this);
        }

        return $this;
    }

    public function removePackageForCategory(PackageForCategory $packageForCategory): self
    {
        if ($this->packageForCategories->contains($packageForCategory)) {
            $this->packageForCategories->removeElement($packageForCategory);
            // set the owning side to null (unless already changed)
            if ($packageForCategory->getPackage() === $this) {
                $packageForCategory->setPackage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PaymentForPackage[]
     */
    public function getPaymentForPackage(): Collection
    {
        return $this->paymentForPackage;
    }

    public function addPaymentForPackage(PaymentForPackage $paymentForPackage): self
    {
        if (!$this->paymentForPackage->contains($paymentForPackage)) {
            $this->paymentForPackage[] = $paymentForPackage;
            $paymentForPackage->setPackage($this);
        }

        return $this;
    }

    public function removePaymentPackage(PaymentForPackage $paymentForPackage): self
    {
        if ($this->paymentForPackage->contains($paymentForPackage)) {
            $this->paymentForPackage->removeElement($paymentForPackage);
            // set the owning side to null (unless already changed)
            if ($paymentForPackage->getPackage() === $this) {
                $paymentForPackage->setPackage(null);
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
