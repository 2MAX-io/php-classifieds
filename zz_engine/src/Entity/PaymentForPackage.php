<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class PaymentForPackage
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
     * @var Payment
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Payment", inversedBy="paymentForPackage")
     * @ORM\JoinColumn(nullable=false)
     */
    private $payment;

    /**
     * @var Package
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Package", inversedBy="paymentForPackage")
     * @ORM\JoinColumn(nullable=false)
     */
    private $package;

    /**
     * @var Listing
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Listing", inversedBy="paymentForPackage")
     * @ORM\JoinColumn(nullable=false)
     */
    private $listing;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPayment(): Payment
    {
        return $this->payment;
    }

    public function setPayment(?Payment $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    public function getPackage(): Package
    {
        return $this->package;
    }

    public function setPackage(?Package $package): self
    {
        $this->package = $package;

        return $this;
    }

    public function getListing(): ?Listing
    {
        return $this->listing;
    }

    public function getListingNotNull(): Listing
    {
        return $this->listing;
    }

    public function setListing(?Listing $listing): self
    {
        $this->listing = $listing;

        return $this;
    }
}
