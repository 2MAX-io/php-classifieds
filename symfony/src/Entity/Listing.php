<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ListingRepository")
 * @ORM\Table(indexes={@Index(columns={"search_text"}, flags={"fulltext"})})
 */
class Listing
{
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
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="listings")
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=5000)
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $email;

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
     * @ORM\Column(type="datetimetz", nullable=false)
     */
    private $firstCreatedDate;

    /**
     * @ORM\Column(type="datetimetz", nullable=false)
     */
    private $validUntilDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $adminConfirmed = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $adminRejected = false;

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
    private $premium = false;

    /**
     * @ORM\Column(type="smallint")
     */
    private $premiumWeight = 0;

    /**
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $premiumUntilDate;

    /**
     * @ORM\Column(type="datetimetz", nullable=false)
     */
    private $lastEditDate;

    /**
     * @ORM\Column(type="datetimetz", nullable=false)
     */
    private $lastReactivationDate;

    /**
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $adminLastConfirmationDate;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $rejectionReason;

    /**
     * @ORM\Column(type="text")
     */
    private $searchText;

    public function __construct()
    {
        $this->listingCustomFieldValues = new ArrayCollection();
        $this->listingFiles = new ArrayCollection();
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
     * @return Collection|ListingFile[]
     */
    public function getListingFiles(): Collection
    {
        return $this->listingFiles->matching(Criteria::create()->orderBy(['sort' => 'asc'])->where(Criteria::expr()->eq("userDeleted", false)));
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

    public function getAdminConfirmed(): ?bool
    {
        return $this->adminConfirmed;
    }

    public function setAdminConfirmed(bool $adminConfirmed): self
    {
        $this->adminConfirmed = $adminConfirmed;

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

    public function getPremium(): ?bool
    {
        return $this->premium;
    }

    public function setPremium(bool $premium): self
    {
        $this->premium = $premium;

        return $this;
    }

    public function getPremiumUntilDate(): ?\DateTimeInterface
    {
        return $this->premiumUntilDate;
    }

    public function setPremiumUntilDate(\DateTimeInterface $premiumUntilDate): self
    {
        $this->premiumUntilDate = $premiumUntilDate;

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

    public function getPremiumWeight(): ?int
    {
        return $this->premiumWeight;
    }

    public function setPremiumWeight(int $premiumWeight): self
    {
        $this->premiumWeight = $premiumWeight;

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

    public function getLastReactivationDate(): ?\DateTimeInterface
    {
        return $this->lastReactivationDate;
    }

    public function setLastReactivationDate(\DateTimeInterface $lastReactivationDate): self
    {
        $this->lastReactivationDate = $lastReactivationDate;

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

    public function getAdminLastConfirmationDate(): ?\DateTimeInterface
    {
        return $this->adminLastConfirmationDate;
    }

    public function setAdminLastConfirmationDate(\DateTimeInterface $adminLastConfirmationDate): self
    {
        $this->adminLastConfirmationDate = $adminLastConfirmationDate;

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
}
