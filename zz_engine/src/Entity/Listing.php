<?php

declare(strict_types=1);

namespace App\Entity;

use App\Helper\ContainerHelper;
use App\Helper\ImageResizePath;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ListingRepository")
 * @ORM\Table(indexes={
 *     @Index(columns={"valid_until_date", "user_deactivated", "user_removed", "admin_activated", "admin_rejected", "admin_removed", "featured", "featured_weight", "order_by_date"}, name="IDX_public_listings"),
 *     @Index(columns={"admin_activated", "admin_removed", "user_removed", "user_deactivated", "admin_rejected"}, name="IDX_activated"),
 *     @Index(columns={"featured", "valid_until_date", "user_deactivated", "user_removed", "admin_activated", "admin_removed"}, name="IDX_featured"),
 *     @Index(columns={"category_id", "valid_until_date", "user_removed", "user_deactivated", "admin_activated", "admin_removed", "featured", "featured_weight", "order_by_date"}, name="IDX_public_listings_cat"),
 *     @Index(columns={"valid_until_date", "user_removed", "user_deactivated", "admin_activated", "admin_removed", "first_created_date"}, name="IDX_latest_listings"),
 *     @Index(columns={"first_created_date"}, name="IDX_first_created_date"),
 *     @Index(columns={"user_id", "user_removed", "last_edit_date"}, name="IDX_user_listings"),
 *     @Index(columns={"search_text"}, flags={"fulltext"}, name="IDX_fulltext_search"),
 *     @Index(columns={"search_text", "email", "phone", "rejection_reason"}, flags={"fulltext"}, name="IDX_fulltext_search_admin")
 * })
 */
class Listing
{
    public const STATUS_ACTIVE = 'STATUS_ACTIVE';
    public const STATUS_ACTIVE_FEATURED = 'STATUS_ACTIVE_FEATURED';
    public const STATUS_EXPIRED = 'STATUS_EXPIRED';
    public const STATUS_PENDING = 'STATUS_PENDING';
    public const STATUS_REJECTED = 'STATUS_REJECTED';
    public const STATUS_DEACTIVATED = 'STATUS_DEACTIVATED';
    public const STATUS_USER_REMOVED = 'STATUS_USER_REMOVED';
    public const STATUS_ADMIN_REMOVED = 'STATUS_ADMIN_REMOVED';

    public const PRICE_FOR_IN_DESCRIPTION = 'PRICE_FOR_IN_DESCRIPTION';
    public const PRICE_FOR_WHOLE = 'PRICE_FOR_WHOLE';
    public const PRICE_FOR_NETTO = 'PRICE_FOR_NETTO';
    public const PRICE_FOR_BRUTTO = 'PRICE_FOR_BRUTTO';
    public const PRICE_FOR_ITEM = 'PRICE_FOR_ITEM';
    public const PRICE_FOR_MINUTE = 'PRICE_FOR_MINUTE';
    public const PRICE_FOR_HOUR = 'PRICE_FOR_HOUR';
    public const PRICE_FOR_SET = 'PRICE_FOR_SET';
    public const PRICE_FOR_ACRE = 'PRICE_FOR_ACRE';
    public const PRICE_FOR_AR = 'PRICE_FOR_AR';
    public const PRICE_FOR_HECTARE = 'PRICE_FOR_HECTARE';
    public const PRICE_FOR_LITER = 'PRICE_FOR_LITER';
    public const PRICE_FOR_BAG = 'PRICE_FOR_BAG';
    public const PRICE_FOR_PACK = 'PRICE_FOR_PACK';
    public const PRICE_FOR_KILOGRAM = 'PRICE_FOR_KILOGRAM';
    public const PRICE_FOR_GRAM = 'PRICE_FOR_GRAM';
    public const PRICE_FOR_TONE = 'PRICE_FOR_TONE';
    public const PRICE_FOR_CM = 'PRICE_FOR_CM';
    public const PRICE_FOR_METER = 'PRICE_FOR_METER';
    public const PRICE_FOR_KILOMETER = 'PRICE_FOR_KILOMETER';
    public const PRICE_FOR_STERE = 'PRICE_FOR_STERE';
    public const PRICE_FOR_MONTH = 'PRICE_FOR_MONTH';
    public const PRICE_FOR_WEEK = 'PRICE_FOR_WEEK';
    public const PRICE_FOR_DAY = 'PRICE_FOR_DAY';
    public const PRICE_FOR_YEAR = 'PRICE_FOR_YEAR';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="listings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="listings")
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=10100)
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $priceFor;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $priceNegotiable;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $emailShow;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $city;

    /**
     * @var ListingCustomFieldValue[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\ListingCustomFieldValue", mappedBy="listing")
     */
    private $listingCustomFieldValues;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ListingFile", mappedBy="listing")
     */
    private $listingFiles;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $mainImage;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $firstCreatedDate;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $validUntilDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $adminActivated = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $adminRemoved = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $userRemoved = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $userDeactivated = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $featured = false;

    /**
     * @ORM\Column(type="smallint")
     */
    private $featuredWeight = 0;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $featuredUntilDate;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $lastEditDate;

    /**
     * used to sort listings
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $orderByDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $adminLastActivationDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $adminRejected = false;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $rejectionReason;

    /**
     * @ORM\Column(type="string", length=100, nullable=false, unique=false)
     */
    private $slug;

    /**
     * @ORM\Column(type="text")
     */
    private $searchText;

    /**
     * @ORM\OneToMany(targetEntity="PaymentForFeaturedPackage", mappedBy="listing")
     */
    private $paymentFeaturedPackage;

    public function __construct()
    {
        $this->listingCustomFieldValues = new ArrayCollection();
        $this->listingFiles = new ArrayCollection();
        $this->paymentFeaturedPackage = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

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

    /**
     * @return Collection|ListingCustomFieldValue[]
     */
    public function getListingCustomFieldValues(): Collection
    {
        return $this->listingCustomFieldValues;
    }

    public function addListingCustomFieldValue(ListingCustomFieldValue $listingCustomFieldValue): self
    {
        if (!$this->listingCustomFieldValues->contains($listingCustomFieldValue)) {
            $this->listingCustomFieldValues[] = $listingCustomFieldValue;
            $listingCustomFieldValue->setListing($this);
        }

        return $this;
    }

    public function removeListingCustomFieldValue(ListingCustomFieldValue $listingCustomFieldValue): self
    {
        if ($this->listingCustomFieldValues->contains($listingCustomFieldValue)) {
            $this->listingCustomFieldValues->removeElement($listingCustomFieldValue);
            // set the owning side to null (unless already changed)
            if ($listingCustomFieldValue->getListing() === $this) {
                $listingCustomFieldValue->setListing(null);
            }
        }

        return $this;
    }

    /**
     * @return ArrayCollection|ListingFile[]
     */
    public function getListingFiles(): Collection
    {
        return $this->listingFiles->matching(Criteria::create()->orderBy(['sort' => 'asc'])->where(Criteria::expr()->eq("userRemoved", false)));
    }

    public function getMainImageNoCache(): ?ListingFile
    {
        return $this->getListingFiles()->first() ?? null;
    }

    public function addListingFile(ListingFile $listingFile): self
    {
        if (!$this->listingFiles->contains($listingFile)) {
            $this->listingFiles[] = $listingFile;
            $listingFile->setListing($this);
        }

        return $this;
    }

    public function removeListingFile(ListingFile $listingFile): self
    {
        if ($this->listingFiles->contains($listingFile)) {
            $this->listingFiles->removeElement($listingFile);
            // set the owning side to null (unless already changed)
            if ($listingFile->getListing() === $this) {
                $listingFile->setListing(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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

    public function getFirstCreatedDate(): ?\DateTimeInterface
    {
        return $this->firstCreatedDate;
    }

    public function setFirstCreatedDate(\DateTimeInterface $firstCreatedDate): self
    {
        $this->firstCreatedDate = $firstCreatedDate;

        return $this;
    }

    public function getAdminActivated(): ?bool
    {
        return $this->adminActivated;
    }

    public function setAdminActivated(bool $adminActivated): self
    {
        $this->adminActivated = $adminActivated;

        return $this;
    }

    public function getUserDeactivated(): ?bool
    {
        return $this->userDeactivated;
    }

    public function setUserDeactivated(bool $userDeactivated): self
    {
        $this->userDeactivated = $userDeactivated;

        return $this;
    }

    public function getFeatured(): ?bool
    {
        return $this->featured;
    }

    public function setFeatured(bool $featured): self
    {
        $this->featured = $featured;

        return $this;
    }

    public function getFeaturedUntilDate(): ?\DateTimeInterface
    {
        return $this->featuredUntilDate;
    }

    public function setFeaturedUntilDate(?\DateTimeInterface $featuredUntilDate = null): self
    {
        $this->featuredUntilDate = $featuredUntilDate;

        return $this;
    }

    public function getValidUntilDate(): ?\DateTimeInterface
    {
        return $this->validUntilDate;
    }

    public function setValidUntilDate(\DateTimeInterface $validUntilDate): self
    {
        $this->validUntilDate = $validUntilDate;

        return $this;
    }

    public function getFeaturedWeight(): ?int
    {
        return $this->featuredWeight;
    }

    public function setFeaturedWeight(int $featuredWeight): self
    {
        $this->featuredWeight = $featuredWeight;

        return $this;
    }

    public function getLastEditDate(): ?\DateTimeInterface
    {
        return $this->lastEditDate;
    }

    public function setLastEditDate(\DateTimeInterface $lastEditDate): self
    {
        $this->lastEditDate = $lastEditDate;

        return $this;
    }

    public function getOrderByDate(): ?\DateTimeInterface
    {
        return $this->orderByDate;
    }

    public function setOrderByDate(\DateTimeInterface $orderByDate): self
    {
        $this->orderByDate = $orderByDate;

        return $this;
    }

    public function getSearchText(): ?string
    {
        return $this->searchText;
    }

    public function setSearchText(string $searchText): self
    {
        $this->searchText = $searchText;

        return $this;
    }

    public function getAdminRemoved(): ?bool
    {
        return $this->adminRemoved;
    }

    public function setAdminRemoved(bool $adminRemoved): self
    {
        $this->adminRemoved = $adminRemoved;

        return $this;
    }

    public function getAdminLastActivationDate(): ?\DateTimeInterface
    {
        return $this->adminLastActivationDate;
    }

    public function setAdminLastActivationDate(\DateTimeInterface $adminLastActivationDate): self
    {
        $this->adminLastActivationDate = $adminLastActivationDate;

        return $this;
    }

    public function getRejectionReason(): ?string
    {
        return $this->rejectionReason;
    }

    public function setRejectionReason(?string $rejectionReason): self
    {
        $this->rejectionReason = $rejectionReason;

        return $this;
    }

    public function getAdminRejected(): ?bool
    {
        return $this->adminRejected;
    }

    public function setAdminRejected(bool $adminRejected): self
    {
        $this->adminRejected = $adminRejected;

        return $this;
    }

    public function getStatus(): string
    {
        if ($this->getAdminRemoved()) {
            return static::STATUS_ADMIN_REMOVED;
        }

        if ($this->getAdminRejected()) {
            return static::STATUS_REJECTED;
        }

        if ($this->getUserRemoved()) {
            return static::STATUS_USER_REMOVED;
        }

        if ($this->getUserDeactivated()) {
            return static::STATUS_DEACTIVATED;
        }

        if ($this->getValidUntilDate() <= new \DateTime()) {
            return static::STATUS_EXPIRED;
        }

        if (false === $this->getAdminActivated() && ContainerHelper::getSettings()->getRequireListingAdminActivation()) {
            return static::STATUS_PENDING;
        }

        if ($this->getFeatured() && $this->getFeaturedUntilDate() >= new \DateTime()) {
            return self::STATUS_ACTIVE_FEATURED;
        }

        return self::STATUS_ACTIVE;
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

    public function getEmailShow(): ?bool
    {
        return $this->emailShow;
    }

    public function setEmailShow(bool $emailShow): self
    {
        $this->emailShow = $emailShow;

        return $this;
    }

    public function getMainImage(string $type = null): ?string
    {
        if ($this->mainImage === null) {
            return null;
        }

        if ($type !== null) {
            return ImageResizePath::forType($type, $this->mainImage);
        }

        return $this->mainImage;
    }

    public function getMainImageInListSize(): ?string
    {
        return $this->getMainImage(ImageResizePath::LIST);
    }

    public function setMainImage(?string $mainImage): self
    {
        $this->mainImage = $mainImage;

        return $this;
    }

    public function getPriceFor(): ?string
    {
        return $this->priceFor;
    }

    public function setPriceFor(?string $priceFor): self
    {
        $this->priceFor = $priceFor;

        return $this;
    }

    public function getPriceNegotiable(): ?bool
    {
        return $this->priceNegotiable;
    }

    public function setPriceNegotiable(?bool $priceNegotiable): self
    {
        $this->priceNegotiable = $priceNegotiable;

        return $this;
    }

    public function isFeaturedActive(): bool
    {
        return $this->getFeaturedUntilDate() > new \DateTime();
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
            $paymentFeaturedPackage->setListing($this);
        }

        return $this;
    }

    public function removePaymentFeaturedPackage(PaymentForFeaturedPackage $paymentFeaturedPackage): self
    {
        if ($this->paymentFeaturedPackage->contains($paymentFeaturedPackage)) {
            $this->paymentFeaturedPackage->removeElement($paymentFeaturedPackage);
            // set the owning side to null (unless already changed)
            if ($paymentFeaturedPackage->getListing() === $this) {
                $paymentFeaturedPackage->setListing(null);
            }
        }

        return $this;
    }
}
