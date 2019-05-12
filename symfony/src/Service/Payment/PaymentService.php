<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Entity\FeaturedPackage;
use App\Entity\Listing;
use App\Entity\Payment;
use App\Entity\PaymentForFeaturedPackage;
use App\Entity\PaymentForBalanceTopUp;
use App\Entity\User;
use App\Security\CurrentUserService;
use App\Service\Payment\Method\PayPalPaymentMethod;
use App\Service\Setting\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

    /**
     * @var SettingsService
     */
    private $settingsService;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    public function __construct(
        PayPalPaymentMethod $payPalPaymentMethod,
        EntityManagerInterface $em,
        SettingsService $settingsService,
        CurrentUserService $currentUserService,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->payPalPaymentMethod = $payPalPaymentMethod;
        $this->em = $em;
        $this->settingsService = $settingsService;
        $this->urlGenerator = $urlGenerator;
        $this->currentUserService = $currentUserService;
    }

    public function createPaymentForFeaturedPackage(Listing $listing, FeaturedPackage $featuredPackage): PaymentDto
    {
        $paymentDto = new PaymentDto();
        $paymentDto->setCurrency($this->settingsService->getCurrency());
        $paymentDto->setAmount($featuredPackage->getPrice());
        $paymentDto->setGatewayCancelUrl(
            $this->urlGenerator->generate(
                'app_user_feature_listing',
                ['id' => $listing->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
        );
        $paymentDto->setUser($this->currentUserService->getUser());
        $paymentDto = $this->createPayment($paymentDto);

        $paymentFeaturedPackage = new PaymentForFeaturedPackage();
        $paymentFeaturedPackage->setPayment($paymentDto->getPaymentEntity());
        $paymentFeaturedPackage->setFeaturedPackage($featuredPackage);
        $paymentFeaturedPackage->setListing($listing);
        $this->em->persist($paymentFeaturedPackage);

        return $paymentDto;
    }

    public function createPaymentForTopUp(User $user, int $amount): PaymentDto
    {
        $paymentDto = new PaymentDto();
        $paymentDto->setCurrency($this->settingsService->getCurrency());
        $paymentDto->setAmount($amount);
        $paymentDto->setUser($this->currentUserService->getUser());

        $paymentDto = $this->createPayment($paymentDto);

        $paymentForBalanceTopUp = new PaymentForBalanceTopUp();
        $paymentForBalanceTopUp->setPayment($paymentDto->getPaymentEntity());
        $paymentForBalanceTopUp->setUser($user);
        $this->em->persist($paymentForBalanceTopUp);

        return $paymentDto;
    }

    public function createPayment(PaymentDto $paymentDto): PaymentDto
    {
        $this->payPalPaymentMethod->createPayment($paymentDto);

        $paymentEntity = new Payment();
        $paymentEntity->setCanceled(false);
        $paymentEntity->setDatetime(new \DateTime());
        $paymentEntity->setAmount($paymentDto->getAmount());
        $paymentEntity->setGatewayTransactionId($paymentDto->getGatewayTransactionId());
        $paymentEntity->setGatewayToken($paymentDto->getGatewayToken());
        $paymentEntity->setGatewayStatus($paymentDto->getGatewayStatus());
        $paymentEntity->setUser($paymentDto->getUser());
        $paymentEntity->setBalanceUpdated(false);
        $this->em->persist($paymentEntity);

        $paymentDto->setPaymentEntity($paymentEntity);

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
