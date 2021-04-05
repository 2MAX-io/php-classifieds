<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\System\ListingInternalData;
use App\Helper\ContainerHelper;
use App\Helper\DateHelper;
use App\Helper\JsonHelper;
use App\Helper\ResizedImagePath;
use App\Service\Listing\CustomField\Dto\CustomFieldInlineDto;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ExpressionBuilder;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ListingRepository")
 * @ORM\Table(indexes={
 *     @Index(columns={"user_deactivated", "valid_until_date", "user_removed", "admin_activated", "admin_rejected", "admin_removed", "featured", "featured_weight", "order_by_date", "id"}, name="IDX_public_listings"),
 *     @Index(columns={"admin_activated", "admin_removed", "user_removed", "user_deactivated", "admin_rejected"}, name="IDX_activated"),
 *     @Index(columns={"featured", "user_deactivated", "valid_until_date", "user_removed", "admin_activated", "admin_removed"}, name="IDX_featured"),
 *     @Index(columns={"category_id", "user_deactivated", "valid_until_date", "user_removed", "admin_activated", "admin_removed", "featured", "featured_weight", "order_by_date", "id"}, name="IDX_public_listings_cat"),
 *     @Index(columns={"category_id", "user_deactivated", "valid_until_date", "user_removed", "admin_activated", "admin_removed", "price", "featured", "featured_weight", "order_by_date", "id"}, name="IDX_public_filtered"),
 *     @Index(columns={"user_deactivated", "valid_until_date", "user_removed", "admin_activated", "admin_removed", "first_created_date"}, name="IDX_latest_listings"),
 *     @Index(columns={"first_created_date"}, name="IDX_first_created_date"),
 *     @Index(columns={"user_id", "user_removed", "last_edit_date"}, name="IDX_user_listings"),
 *     @Index(columns={"search_text"}, flags={"fulltext"}, name="IDX_fulltext_search"),
 *     @Index(columns={"search_text", "email", "phone", "rejection_reason"}, flags={"fulltext"}, name="IDX_fulltext_search_admin"),
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

    public const PRICE_FOR_SEE_DESCRIPTION = 'PRICE_FOR_SEE_DESCRIPTION';
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="listings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @var User
     *
     * @Assert\NotNull(groups={"skipAutomaticValidation"})
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="listings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @var \DateTimeInterface
     *
     * @Assert\NotNull(groups={"skipAutomaticValidation"})
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $validUntilDate;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $adminActivated = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $adminRejected = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $adminRemoved = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $userDeactivated = false;

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
    private $featured = false;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $featuredUntilDate;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=false)
     */
    private $featuredWeight = 0;

    /**
     * used to sort listings
     *
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $orderByDate;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $firstCreatedDate;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $lastEditDate;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $adminLastActivationDate;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=70, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=12000, nullable=false)
     */
    private $description;

    /**
     * @var float|null
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $price;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $priceFor;

    /**
     * @var bool|null
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $priceNegotiable;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $phone;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=70, nullable=true)
     */
    private $email;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $emailShow;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $location;

    /**
     * @var float|null
     *
     * @ORM\Column(type="float", nullable=true)
     * @Assert\Type(type="numeric")
     */
    private $locationLatitude;

    /**
     * @var float|null
     *
     * @ORM\Column(type="float", nullable=true)
     * @Assert\Type(type="numeric")
     */
    private $locationLongitude;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mainImage;

    /**
     * @var string
     *
     * @Assert\NotNull(groups={"skipAutomaticValidation"})
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $slug;

    /**
     * @var string
     *
     * @Assert\NotNull(groups={"skipAutomaticValidation"})
     * @ORM\Column(type="text", nullable=false)
     */
    private $searchText;

    /**
     * @var string|null
     *
     * @Assert\NotNull(groups={"skipAutomaticValidation"})
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $customFieldsInlineJson;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $rejectionReason;

    /**
     * @var Collection|PaymentForPackage[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\PaymentForPackage", mappedBy="listing")
     */
    private $paymentForPackage;

    /**
     * @var Collection|ListingCustomFieldValue[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\ListingCustomFieldValue", mappedBy="listing")
     */
    private $listingCustomFieldValues;

    /**
     * @var ArrayCollection|ListingFile[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\ListingFile", mappedBy="listing")
     */
    private $listingFiles;

    /**
     * @var Collection|ListingInternalData[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\System\ListingInternalData", mappedBy="listing", fetch="EXTRA_LAZY")
     */
    private $listingInternalData;

    /**
     * @var Collection|UserObservedListing[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\UserObservedListing", mappedBy="listing", fetch="EXTRA_LAZY")
     */
    private $userObservedListings;

    public function __construct()
    {
        $this->listingCustomFieldValues = new ArrayCollection();
        $this->listingFiles = new ArrayCollection();
        $this->paymentForPackage = new ArrayCollection();
        $this->listingInternalData = new ArrayCollection();
        $this->userObservedListings = new ArrayCollection();
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

        if ($this->isExpired()) {
            return static::STATUS_EXPIRED;
        }

        if (false === $this->getAdminActivated() && ContainerHelper::getSettings()->getRequireListingAdminActivation()) {
            return static::STATUS_PENDING;
        }

        if ($this->getFeatured() && $this->getFeaturedUntilDate() >= DateHelper::create()) {
            return self::STATUS_ACTIVE_FEATURED;
        }

        return self::STATUS_ACTIVE;
    }

    public function isFeaturedActive(): bool
    {
        return $this->getFeatured() && $this->getFeaturedUntilDate() > DateHelper::create();
    }

    /**
     * @return ArrayCollection|ListingFile[]
     */
    public function getListingFiles(): ArrayCollection
    {
        $expr = Criteria::expr();
        if (!$expr instanceof ExpressionBuilder) {
            throw new \RuntimeException('Criteria::expr() returns null');
        }

        return $this->listingFiles->matching(
            Criteria::create()
                ->orderBy(['sort' => Criteria::ASC])
                ->where($expr->eq('userRemoved', false))
        );
    }

    /**
     * @return Collection|ListingFile[]
     */
    public function getListingFilesAll(): Collection
    {
        return $this->listingFiles;
    }

    public function getMainImageNoCache(): ?ListingFile
    {
        return $this->getListingFiles()->first() ?: null;
    }

    public function getMainImage(string $type = null): ?string
    {
        if (null === $this->mainImage) {
            return null;
        }

        if (null !== $type) {
            return ResizedImagePath::forType($type, $this->mainImage);
        }

        return $this->mainImage;
    }

    public function getMainImageInListSize(): ?string
    {
        return $this->getMainImage(ResizedImagePath::LIST);
    }

    public function getValidUntilDateStringOrNull(): ?string
    {
        if (!$this->getValidUntilDate()) {
            return null;
        }

        return $this->getValidUntilDate()->format(DateHelper::MYSQL_FORMAT);
    }

    public function isExpired(): bool
    {
        return $this->getValidUntilDate() < DateHelper::create()->setTime(0, 0);
    }

    public function getHasLocationOnMap(): bool
    {
        return $this->getLocationLatitude() && $this->getLocationLongitude();
    }

    /**
     * @return CustomFieldInlineDto[]
     */
    public function getCustomFieldsInline(): array
    {
        $return = [];

        try {
            $customFieldListArray = JsonHelper::toArray($this->getCustomFieldsInlineJson()) ?? [];
            foreach ($customFieldListArray as $customFieldArray) {
                $return[] = CustomFieldInlineDto::fromArray($customFieldArray);
            }
        } catch (\Throwable $e) {
            return [];
        }

        return $return;
    }

    public function isObserved(): bool
    {
        return $this->getUserObservedListings()->count() > 0;
    }

    public function hasContactData(): bool
    {
        return !empty($this->getPhone()) || (!empty($this->getEmail()) && $this->getEmailShow());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdNotNull(): int
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

    public function getPrice(): ?float
    {
        return $this->price ?? null;
    }

    public function setPrice(?float $price): self
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

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function getCategoryNotNull(): Category
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

    public function getUserNotNull(): User
    {
        if (null === $this->user) {
            throw new \RuntimeException('user is null');
        }

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

    public function setAdminLastActivationDate(?\DateTimeInterface $adminLastActivationDate): self
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

    public function getLocationLongitude(): ?float
    {
        return $this->locationLongitude;
    }

    public function setLocationLongitude(?float $locationLongitude): void
    {
        $this->locationLongitude = $locationLongitude;
    }

    public function getLocationLatitude(): ?float
    {
        return $this->locationLatitude;
    }

    public function setLocationLatitude(?float $locationLatitude): void
    {
        $this->locationLatitude = $locationLatitude;
    }

    public function getListingInternalData(): ?ListingInternalData
    {
        return $this->listingInternalData->first() ?: null;
    }

    public function addListingInternalData(ListingInternalData $listingInternalData): self
    {
        if (!$this->listingInternalData->contains($listingInternalData)) {
            $this->listingInternalData[] = $listingInternalData;
            $listingInternalData->setListing($this);
        }

        return $this;
    }

    public function removeListingInternalData(ListingInternalData $listingInternalData): self
    {
        // set the owning side to null (unless already changed)
        if ($this->listingInternalData->removeElement($listingInternalData)
            && $listingInternalData->getListing() === $this
        ) {
            $listingInternalData->setListing(null);
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

    public function addPaymentPackage(PaymentForPackage $paymentForPackage): self
    {
        if (!$this->paymentForPackage->contains($paymentForPackage)) {
            $this->paymentForPackage[] = $paymentForPackage;
            $paymentForPackage->setListing($this);
        }

        return $this;
    }

    public function removePaymentPackage(PaymentForPackage $paymentForPackage): self
    {
        // set the owning side to null (unless already changed)
        if ($this->paymentForPackage->removeElement($paymentForPackage)
            && $paymentForPackage->getListing() === $this
        ) {
            $paymentForPackage->setListing(null);
        }

        return $this;
    }

    public function getCustomFieldsInlineJson(): ?string
    {
        return $this->customFieldsInlineJson;
    }

    public function setCustomFieldsInlineJson(?string $customFieldsInlineJson): void
    {
        $this->customFieldsInlineJson = $customFieldsInlineJson;
    }

    /**
     * @return Collection|UserObservedListing[]
     */
    public function getUserObservedListings(): Collection
    {
        return $this->userObservedListings;
    }

    public function addUserObservedListing(UserObservedListing $userObservedListing): self
    {
        if (!$this->userObservedListings->contains($userObservedListing)) {
            $this->userObservedListings[] = $userObservedListing;
            $userObservedListing->setListing($this);
        }

        return $this;
    }

    public function removeUserObservedListing(UserObservedListing $userObservedListing): self
    {
        // set the owning side to null (unless already changed)
        if ($this->userObservedListings->removeElement($userObservedListing) && $userObservedListing->getListing() === $this) {
            $userObservedListing->setListing(null);
        }

        return $this;
    }
}
