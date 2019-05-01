<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Entity\FeaturedPackage;
use App\Entity\Payment;
use App\Service\Payment\Method\PayPalPaymentMethod;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class PaymentService
{
    /**
     * @var PayPalPaymentMethod
     */
    private $payPalPaymentMethod;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(PayPalPaymentMethod $payPalPaymentMethod, EntityManagerInterface $em)
    {
        $this->payPalPaymentMethod = $payPalPaymentMethod;
        $this->em = $em;
    }

    public function createPaymentForFeaturedPackage(FeaturedPackage $featuredPackage): PaymentDto
    {
        $paymentDto = new PaymentDto();
        $paymentDto->setCurrency('PLN');
        $paymentDto->setAmount($featuredPackage->getPrice());

        return $this->createPayment($paymentDto);
    }

    public function createPayment(PaymentDto $paymentDto): PaymentDto
    {
        $this->payPalPaymentMethod->createPayment($paymentDto);

        $paymentEntity = new Payment();
        $paymentEntity->setDatetime(new \DateTime());
        $paymentEntity->setAmount($paymentDto->getAmount());
        $paymentEntity->setGatewayTransactionId($paymentDto->getGatewayTransactionId());
        $paymentEntity->setGatewayStatus($paymentDto->getGatewayStatus());
        $paymentEntity->setBalanceUpdated(false);
        $this->em->persist($paymentEntity);

        return $paymentDto;
    }

    public function markBalanceUpdated(ConfirmPaymentDto $confirmPaymentDto): void
    {
        $paymentEntity = $this->getPaymentEntity($confirmPaymentDto);

        $paymentEntity->setBalanceUpdated(true);
        $paymentEntity->setGatewayStatus($confirmPaymentDto->getGatewayStatus());

        $this->em->persist($paymentEntity);

    }

    public function isBalanceUpdated(ConfirmPaymentDto $confirmPaymentDto): bool
    {
        return $this->getPaymentEntity($confirmPaymentDto)->getBalanceUpdated();
    }

    public function confirmPayment(Request $request, ConfirmPaymentDto $confirmPaymentDto): ConfirmPaymentDto
    {
        $confirmPaymentDto = $this->payPalPaymentMethod->confirmPayment($request, $confirmPaymentDto);

        return $confirmPaymentDto;
    }

    public function getPaymentEntity(ConfirmPaymentDto $confirmPaymentDto): ?Payment
    {
        return $this->em->getRepository(Payment::class)->findOneBy(
            ['gatewayTransactionId' => $confirmPaymentDto->getGatewayTransactionId()]
        );
    }
}
