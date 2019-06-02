<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ListingPoliceLogRepository")
 * @ORM\Table(name="zzzz_listing_police_log")
 */
class ListingPoliceLog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $sourceIp;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $destinationIp;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datetime;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $listingId;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $userId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSourceIp(): ?string
    {
        return $this->sourceIp;
    }

    public function setSourceIp(string $sourceIp): self
    {
        $this->sourceIp = $sourceIp;

        return $this;
    }

    public function getDestinationIp(): ?string
    {
        return $this->destinationIp;
    }

    public function setDestinationIp(string $destinationIp): self
    {
        $this->destinationIp = $destinationIp;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

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

    public function getListingId(): ?int
    {
        return $this->listingId;
    }

    public function setListingId(int $listingId): self
    {
        $this->listingId = $listingId;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }
}
