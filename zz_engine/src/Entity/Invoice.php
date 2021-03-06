<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InvoiceRepository")
 */
class Invoice
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", nullable=false)
     */
    private $id;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="invoices")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * @var Payment|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Payment", inversedBy="invoices")
     * @ORM\JoinColumn(nullable=true)
     */
    private $payment;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=100, nullable=true, unique=true)
     */
    private $invoiceNumber;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $invoiceDate;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $totalMoney;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $currency;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $totalTaxMoney;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $externalSystemReference;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $externalSystemReferenceSecondary;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $uuid;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $displayToUser;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $exported;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $sentToUser;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $invoiceFilePath;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $companyName;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $individualClientName;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $clientTaxNumber;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $city;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $street;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $buildingNumber;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $unitNumber;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $zipCode;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $sellerCompanyName;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $sellerTaxNumber;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $sellerCity;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sellerStreet;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $sellerBuildingNumber;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $sellerUnitNumber;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $sellerZipCode;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $sellerCountry;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $sellerEmail;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $emailToSendInvoice;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $exportDate;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $sentToUserDate;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $createdDate;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updatedDate;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(?Payment $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    public function getDisplayToUser(): bool
    {
        return $this->displayToUser;
    }

    public function setDisplayToUser(bool $displayToUser): void
    {
        $this->displayToUser = $displayToUser;
    }

    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber;
    }

    public function setInvoiceNumber(string $invoiceNumber): self
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    public function getInvoiceDate(): ?\DateTimeInterface
    {
        return $this->invoiceDate;
    }

    public function setInvoiceDate(\DateTimeInterface $invoiceDate): self
    {
        $this->invoiceDate = $invoiceDate;

        return $this;
    }

    public function getExternalSystemReference(): ?string
    {
        return $this->externalSystemReference;
    }

    public function setExternalSystemReference(string $externalSystemReference): self
    {
        $this->externalSystemReference = $externalSystemReference;

        return $this;
    }

    public function getExternalSystemReferenceSecondary(): ?string
    {
        return $this->externalSystemReferenceSecondary;
    }

    public function setExternalSystemReferenceSecondary(string $externalSystemReferenceSecondary): self
    {
        $this->externalSystemReferenceSecondary = $externalSystemReferenceSecondary;

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getExported(): ?bool
    {
        return $this->exported;
    }

    public function setExported(bool $exported): self
    {
        $this->exported = $exported;

        return $this;
    }

    public function getSentToUser(): ?bool
    {
        return $this->sentToUser;
    }

    public function setSentToUser(bool $sentToUser): self
    {
        $this->sentToUser = $sentToUser;

        return $this;
    }

    public function getInvoiceFilePath(): ?string
    {
        return $this->invoiceFilePath;
    }

    public function setInvoiceFilePath(?string $invoiceFilePath): self
    {
        $this->invoiceFilePath = $invoiceFilePath;

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getBuildingNumber(): ?string
    {
        return $this->buildingNumber;
    }

    public function setBuildingNumber(string $buildingNumber): self
    {
        $this->buildingNumber = $buildingNumber;

        return $this;
    }

    public function getUnitNumber(): ?string
    {
        return $this->unitNumber;
    }

    public function setUnitNumber(?string $unitNumber): self
    {
        $this->unitNumber = $unitNumber;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(?string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getEmailToSendInvoice(): ?string
    {
        return $this->emailToSendInvoice;
    }

    public function setEmailToSendInvoice(string $emailToSendInvoice): self
    {
        $this->emailToSendInvoice = $emailToSendInvoice;

        return $this;
    }

    public function getExportDate(): ?\DateTimeInterface
    {
        return $this->exportDate;
    }

    public function setExportDate(\DateTimeInterface $exportDate): self
    {
        $this->exportDate = $exportDate;

        return $this;
    }

    public function getSentToUserDate(): ?\DateTimeInterface
    {
        return $this->sentToUserDate;
    }

    public function setSentToUserDate(\DateTimeInterface $sentToUserDate): self
    {
        $this->sentToUserDate = $sentToUserDate;

        return $this;
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->createdDate;
    }

    public function setCreatedDate(\DateTimeInterface $createdDate): self
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    public function getUpdatedDate(): ?\DateTimeInterface
    {
        return $this->updatedDate;
    }

    public function setUpdatedDate(\DateTimeInterface $updatedDate): self
    {
        $this->updatedDate = $updatedDate;

        return $this;
    }

    public function getClientTaxNumber(): ?string
    {
        return $this->clientTaxNumber;
    }

    public function setClientTaxNumber(?string $clientTaxNumber): self
    {
        $this->clientTaxNumber = $clientTaxNumber;

        return $this;
    }

    public function getTotalMoney(): string
    {
        return $this->totalMoney;
    }

    public function getTotalMoneyFloat(): float
    {
        return (float) $this->totalMoney * 100;
    }

    public function setTotalMoney(string $totalMoney): void
    {
        $this->totalMoney = $totalMoney;
    }

    public function getTotalTaxMoney(): ?string
    {
        return $this->totalTaxMoney;
    }

    public function setTotalTaxMoney(string $totalTaxMoney): void
    {
        $this->totalTaxMoney = $totalTaxMoney;
    }

    public function getSellerCompanyName(): string
    {
        return $this->sellerCompanyName;
    }

    public function setSellerCompanyName(string $sellerCompanyName): void
    {
        $this->sellerCompanyName = $sellerCompanyName;
    }

    public function getSellerTaxNumber(): ?string
    {
        return $this->sellerTaxNumber;
    }

    public function setSellerTaxNumber(?string $sellerTaxNumber): void
    {
        $this->sellerTaxNumber = $sellerTaxNumber;
    }

    public function getSellerCity(): string
    {
        return $this->sellerCity;
    }

    public function setSellerCity(string $sellerCity): void
    {
        $this->sellerCity = $sellerCity;
    }

    public function getSellerStreet(): ?string
    {
        return $this->sellerStreet;
    }

    public function setSellerStreet(?string $sellerStreet): void
    {
        $this->sellerStreet = $sellerStreet;
    }

    public function getSellerBuildingNumber(): string
    {
        return $this->sellerBuildingNumber;
    }

    public function setSellerBuildingNumber(string $sellerBuildingNumber): void
    {
        $this->sellerBuildingNumber = $sellerBuildingNumber;
    }

    public function getSellerUnitNumber(): ?string
    {
        return $this->sellerUnitNumber;
    }

    public function setSellerUnitNumber(?string $sellerUnitNumber): void
    {
        $this->sellerUnitNumber = $sellerUnitNumber;
    }

    public function getSellerZipCode(): string
    {
        return $this->sellerZipCode;
    }

    public function setSellerZipCode(string $sellerZipCode): void
    {
        $this->sellerZipCode = $sellerZipCode;
    }

    public function getSellerCountry(): ?string
    {
        return $this->sellerCountry;
    }

    public function setSellerCountry(?string $sellerCountry): void
    {
        $this->sellerCountry = $sellerCountry;
    }

    public function getSellerEmail(): ?string
    {
        return $this->sellerEmail;
    }

    public function setSellerEmail(?string $sellerEmail): void
    {
        $this->sellerEmail = $sellerEmail;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getIndividualClientName(): ?string
    {
        return $this->individualClientName;
    }

    public function setIndividualClientName(?string $individualClientName): void
    {
        $this->individualClientName = $individualClientName;
    }
}
