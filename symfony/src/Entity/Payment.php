<?php

declare(strict_types=1);

namespace App\Entity;

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
     * @ORM\Column(type="string", length=255)
     */
    private $gatewayTransactionId;

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
}
