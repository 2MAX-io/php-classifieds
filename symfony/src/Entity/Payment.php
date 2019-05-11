<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaymentRepository")
 */
class Payment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datetime;

    /**
     * @ORM\Column(type="string", length=70)
     */
    private $gatewayStatus;

    /**
     * @ORM\Column(type="boolean")
     */
    private $balanceUpdated;

    /**
     * @ORM\Column(type="boolean")
     */
    private $canceled;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $gatewayTransactionId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $gatewayToken;

    /**
     * @var PaymentFeaturedPackage|null
     *
     * @ORM\OneToOne(targetEntity="App\Entity\PaymentFeaturedPackage", mappedBy="payment", fetch="EXTRA_LAZY")
     */
    private $paymentFeaturedPackage;

    /**
     * @var PaymentForBalanceTopUp|null
     *
     * @ORM\OneToOne(targetEntity="App\Entity\PaymentForBalanceTopUp", mappedBy="payment", fetch="EXTRA_LAZY")
     */
    private $paymentForBalanceTopUp;

    /**
     * @var UserBalanceChange[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\UserBalanceChange", mappedBy="payment", fetch="EXTRA_LAZY")
     */
    private $userBalanceChanges;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="payments")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    public function __construct()
    {
        $this->userBalanceChanges = new ArrayCollection();
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

    public function getBalanceUpdated(): ?bool
    {
        return $this->balanceUpdated;
    }

    public function setBalanceUpdated(bool $balanceUpdated): self
    {
        $this->balanceUpdated = $balanceUpdated;

        return $this;
    }

    public function getGatewayTransactionId(): ?string
    {
        return $this->gatewayTransactionId;
    }

    public function setGatewayTransactionId(string $gatewayTransactionId): self
    {
        $this->gatewayTransactionId = $gatewayTransactionId;

        return $this;
    }

    public function getPaymentFeaturedPackage(): ?PaymentFeaturedPackage
    {
        return $this->paymentFeaturedPackage;
    }

    public function setPaymentFeaturedPackage(?PaymentFeaturedPackage $paymentFeaturedPackage): self
    {
        $this->paymentFeaturedPackage = $paymentFeaturedPackage;

        // set (or unset) the owning side of the relation if necessary
        $newPayment = $paymentFeaturedPackage === null ? null : $this;
        if ($newPayment !== $paymentFeaturedPackage->getPayment()) {
            $paymentFeaturedPackage->setPayment($newPayment);
        }

        return $this;
    }

    public function getGatewayToken(): ?string
    {
        return $this->gatewayToken;
    }

    public function setGatewayToken(string $gatewayToken): self
    {
        $this->gatewayToken = $gatewayToken;

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
        $newPayment = $paymentForBalanceTopUp === null ? null : $this;
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
}
