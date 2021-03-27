<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Payment
{
    public const FOR_FEATURED_PACKAGE_TYPE = 'FOR_FEATURED_PACKAGE_TYPE';
    public const BALANCE_TOP_UP_TYPE = 'BALANCE_TOP_UP_TYPE';

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=false)
     */
    private $id;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="payments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false, length=255)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $amount;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $gatewayAmountPaid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10, nullable=false)
     */
    private $currency;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $datetime;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $gatewayStatus;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $paid;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $delivered;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $canceled;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $gatewayPaymentId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=70, nullable=false)
     */
    private $appPaymentToken;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=70, nullable=false)
     */
    private $gatewayName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=70, nullable=false)
     */
    private $gatewayMode;

    /**
     * @var PaymentForFeaturedPackage|null
     *
     * @ORM\OneToOne(targetEntity="App\Entity\PaymentForFeaturedPackage", mappedBy="payment", fetch="EXTRA_LAZY")
     */
    private $paymentForFeaturedPackage;

    /**
     * @var PaymentForBalanceTopUp|null
     *
     * @ORM\OneToOne(targetEntity="App\Entity\PaymentForBalanceTopUp", mappedBy="payment", fetch="EXTRA_LAZY")
     */
    private $paymentForBalanceTopUp;

    /**
     * @var Collection|UserBalanceChange[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\UserBalanceChange", mappedBy="payment", fetch="EXTRA_LAZY")
     */
    private $userBalanceChanges;

    /**
     * @var Collection|Invoice[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Invoice", mappedBy="payment")
     */
    private $invoices;

    public function __construct()
    {
        $this->userBalanceChanges = new ArrayCollection();
        $this->invoices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getGatewayStatus(): ?string
    {
        return $this->gatewayStatus;
    }

    public function setGatewayStatus(string $gatewayStatus): self
    {
        $this->gatewayStatus = $gatewayStatus;

        return $this;
    }

    public function getPaid(): bool
    {
        return $this->paid;
    }

    public function setPaid(bool $paid): self
    {
        $this->paid = $paid;

        return $this;
    }

    public function getPaymentForFeaturedPackage(): ?PaymentForFeaturedPackage
    {
        return $this->paymentForFeaturedPackage;
    }

    public function setPaymentForFeaturedPackage(?PaymentForFeaturedPackage $paymentForFeaturedPackage): self
    {
        $this->paymentForFeaturedPackage = $paymentForFeaturedPackage;

        // set (or unset) the owning side of the relation if necessary
        $newPayment = null === $paymentForFeaturedPackage ? null : $this;
        if ($newPayment !== $paymentForFeaturedPackage->getPayment()) {
            $paymentForFeaturedPackage->setPayment($newPayment);
        }

        return $this;
    }

    public function getCanceled(): ?bool
    {
        return $this->canceled;
    }

    public function setCanceled(bool $canceled): self
    {
        $this->canceled = $canceled;

        return $this;
    }

    public function getPaymentForBalanceTopUp(): ?PaymentForBalanceTopUp
    {
        return $this->paymentForBalanceTopUp;
    }

    public function setPaymentForBalanceTopUp(?PaymentForBalanceTopUp $paymentForBalanceTopUp): self
    {
        $this->paymentForBalanceTopUp = $paymentForBalanceTopUp;

        // set (or unset) the owning side of the relation if necessary
        $newPayment = null === $paymentForBalanceTopUp ? null : $this;
        if ($newPayment !== $paymentForBalanceTopUp->getPayment()) {
            $paymentForBalanceTopUp->setPayment($newPayment);
        }

        return $this;
    }

    /**
     * @return Collection|UserBalanceChange[]
     */
    public function getUserBalanceChanges(): Collection
    {
        return $this->userBalanceChanges;
    }

    public function addUserBalanceChange(UserBalanceChange $userBalanceChange): self
    {
        if (!$this->userBalanceChanges->contains($userBalanceChange)) {
            $this->userBalanceChanges[] = $userBalanceChange;
            $userBalanceChange->setPayment($this);
        }

        return $this;
    }

    public function removeUserBalanceChange(UserBalanceChange $userBalanceChange): self
    {
        if ($this->userBalanceChanges->contains($userBalanceChange)) {
            $this->userBalanceChanges->removeElement($userBalanceChange);
            // set the owning side to null (unless already changed)
            if ($userBalanceChange->getPayment() === $this) {
                $userBalanceChange->setPayment(null);
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

    public function getGatewayPaymentId(): ?string
    {
        return $this->gatewayPaymentId;
    }

    public function setGatewayPaymentId(string $gatewayPaymentId): self
    {
        $this->gatewayPaymentId = $gatewayPaymentId;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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

    public function getAppPaymentToken(): ?string
    {
        return $this->appPaymentToken;
    }

    public function setAppPaymentToken(string $appPaymentToken): self
    {
        $this->appPaymentToken = $appPaymentToken;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getGatewayAmountPaid(): ?int
    {
        return $this->gatewayAmountPaid;
    }

    public function setGatewayAmountPaid(?int $gatewayAmountPaid): self
    {
        $this->gatewayAmountPaid = $gatewayAmountPaid;

        return $this;
    }

    public function getDelivered(): bool
    {
        return $this->delivered;
    }

    public function setDelivered(bool $delivered): self
    {
        $this->delivered = $delivered;

        return $this;
    }

    public function getGatewayName(): string
    {
        return $this->gatewayName;
    }

    public function setGatewayName(string $gatewayName): void
    {
        $this->gatewayName = $gatewayName;
    }

    public function getGatewayMode(): string
    {
        return $this->gatewayMode;
    }

    public function setGatewayMode(string $gatewayMode): void
    {
        $this->gatewayMode = $gatewayMode;
    }

    /**
     * @return Collection|Invoice[]
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): self
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices[] = $invoice;
            $invoice->setPayment($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): self
    {
        if ($this->invoices->contains($invoice)) {
            $this->invoices->removeElement($invoice);
            // set the owning side to null (unless already changed)
            if ($invoice->getPayment() === $this) {
                $invoice->setPayment(null);
            }
        }

        return $this;
    }
}
