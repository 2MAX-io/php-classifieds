<?php

declare(strict_types=1);

namespace App\Entity\System;

use App\Entity\Listing;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="zzzz_listing_report")
 */
class ListingReport
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
     * @var Listing
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Listing")
     * @ORM\JoinColumn(nullable=false)
     */
    private $listing;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $datetime;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=3000, nullable=false)
     */
    private $reportMessage;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $email;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $removed = false;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $policeLog;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getReportMessage(): ?string
    {
        return $this->reportMessage;
    }

    public function setReportMessage(string $reportMessage): self
    {
        $this->reportMessage = $reportMessage;

        return $this;
    }

    public function getListing(): ?Listing
    {
        return $this->listing;
    }

    public function setListing(?Listing $listing): self
    {
        $this->listing = $listing;

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

    public function getDatetime(): \DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): void
    {
        $this->datetime = $datetime;
    }

    public function getPoliceLog(): string
    {
        return $this->policeLog;
    }

    public function setPoliceLog(string $policeLog): void
    {
        $this->policeLog = $policeLog;
    }

    public function getRemoved(): bool
    {
        return $this->removed;
    }

    public function setRemoved(bool $removed): void
    {
        $this->removed = $removed;
    }
}
