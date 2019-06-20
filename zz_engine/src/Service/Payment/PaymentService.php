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
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @var CurrentUserService
     */
    private $currentUserService;

    /**
     * @var TranslatorInterface
     */
    private $trans;

    public function __construct(
        PayPalPaymentMethod $payPalPaymentMethod,
        EntityManagerInterface $em,
        SettingsService $settingsService,
        CurrentUserService $currentUserService,
        TranslatorInterface $trans
    ) {
        $this->payPalPaymentMethod = $payPalPaymentMethod;
        $this->em = $em;
        $this->settingsService = $settingsService;
        $this->currentUserService = $currentUserService;
        $this->trans = $trans;
    }

    public function createPaymentForFeaturedPackage(Listing $listing, FeaturedPackage $featuredPackage): PaymentDto
    {
        $paymentDto = new PaymentDto();
        $paymentDto->setPaymentType(Payment::FOR_FEATURED_PACKAGE_TYPE);
        $paymentDto->setPaymentDescription(
            $this->trans->trans(
                'trans.Payment for featured listing: %listingInfo%, featured package: %featurePackageName%, price: %price%, featured days: %featuredDays%',
                [
                    '%featurePackageName%' => "{$featuredPackage->getName()} ({$featuredPackage->getAdminName()}) [id:{$featuredPackage->getId()}]",
                    '%price%' => $featuredPackage->getPriceFloat(),
                    '%listingInfo%' => "{$listing->getTitle()} [id: {$listing->getId()}]",
                    '%featuredDays%' => $featuredPackage->getDaysFeaturedExpire(),
                ]
            )
        );
        $paymentDto->setCurrency($this->settingsService->getCurrency());
        $paymentDto->setAmount($featuredPackage->getPrice());
        $paymentDto->setUser($this->currentUserService->getUserOrNull());
        if ($this->settingsService->getSettingsDto()->getPaymentGatewayPaymentDescription()) {
            $paymentDto->setGatewayPaymentDescription($this->settingsService->getSettingsDto()->getPaymentGatewayPaymentDescription());
        } else {
            $paymentDto->setGatewayPaymentDescription($this->trans->trans('trans.Promotion of listings'));
        }

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
        $paymentDto->setPaymentType(Payment::BALANCE_TOP_UP_TYPE);
        $paymentDto->setPaymentDescription(
            $this->trans->trans(
                'trans.Payment for balance top up, amount: %amount%',
                [
                    '%amount%' => $amount / 100,
                ]
            )
        );
        $paymentDto->setCurrency($this->settingsService->getCurrency());
        $paymentDto->setAmount($amount);
        $paymentDto->setUser($this->currentUserService->getUserOrNull());
        if ($this->settingsService->getSettingsDto()->getPaymentGatewayPaymentDescription()) {
            $paymentDto->setGatewayPaymentDescription($this->settingsService->getSettingsDto()->getPaymentGatewayPaymentDescription());
        } else {
            $paymentDto->setGatewayPaymentDescription($this->trans->trans('trans.Promotion of listings'));
        }

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
        $paymentEntity->setBalanceUpdated(false);
        $paymentEntity->setDatetime(new \DateTime());
        $paymentEntity->setAmount($paymentDto->getAmount());
        $paymentEntity->setGatewayPaymentId($paymentDto->getGatewayPaymentId());
        $paymentEntity->setGatewayToken($paymentDto->getGatewayToken());
        $paymentEntity->setGatewayStatus($paymentDto->getGatewayStatus());
        $paymentEntity->setUser($paymentDto->getUser());
        $paymentEntity->setType($paymentDto->getPaymentType());
        $paymentEntity->setDescription($paymentDto->getPaymentDescription());

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

    public function confirmPayment(Request $request): ConfirmPaymentDto
    {
        $confirmPaymentDto = $this->payPalPaymentMethod->confirmPayment($request);

        $paymentEntity = $this->getPaymentEntity($confirmPaymentDto);
        $paymentEntity->setGatewayTransactionId($confirmPaymentDto->getGatewayTransactionId());

        $confirmPaymentDto->setPaymentEntity($paymentEntity);

        return $confirmPaymentDto;
    }

    public function getPaymentEntity(ConfirmPaymentDto $confirmPaymentDto): Payment
    {
        return $this->em->getRepository(Payment::class)->findOneBy(
            ['gatewayPaymentId' => $confirmPaymentDto->getGatewayPaymentId()]
        );
    }
}
