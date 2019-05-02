<?php

declare(strict_types=1);

namespace App\Service\Payment;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentHelperService
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getSuccessUrl(): string
    {
        return $this->urlGenerator->generate('app_payment', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function getCancelUrl(): string
    {
        return $this->urlGenerator->generate('app_payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
