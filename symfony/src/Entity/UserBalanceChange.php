<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserBalanceChangeRepository")
 */
class UserBalanceChange
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userBalanceChanges")
     */
    private $user;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $balanceChange;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $balanceFinal;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datetime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBalanceChange(): ?int
    {
        return $this->balanceChange;
    }

    public function setBalanceChange(int $balanceChange): self
    {
        $this->balanceChange = $balanceChange;

        return $this;
    }

    public function getBalanceFinal(): ?int
    {
        return $this->balanceFinal;
    }

    public function setBalanceFinal(int $balanceFinal): self
    {
        $this->balanceFinal = $balanceFinal;

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
