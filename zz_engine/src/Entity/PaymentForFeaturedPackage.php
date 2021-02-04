<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaymentForFeaturedPackageRepository")
 */
class PaymentForFeaturedPackage
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
     * @ORM\OneToOne(targetEntity="App\Entity\Payment", inversedBy="paymentForFeaturedPackage")
     * @ORM\JoinColumn(nullable=false)
     */
    private $payment;

    /**
     * @var FeaturedPackage
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\FeaturedPackage", inversedBy="paymentFeaturedPackage")
     * @ORM\JoinColumn(nullable=false)
     */
    private $featuredPackage;

    /**
     * @var Listing
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Listing", inversedBy="paymentFeaturedPackage")
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

    public function getFeaturedPackage(): FeaturedPackage
    {
        return $this->featuredPackage;
    }

    public function setFeaturedPackage(FeaturedPackage $featuredPackage): self
    {
        $this->featuredPackage = $featuredPackage;

        return $this;
    }

    public function getListing(): Listing
    {
        return $this->listing;
    }

    public function setListing(Listing $listing): self
    {
        $this->listing = $listing;

        return $this;
    }
}
