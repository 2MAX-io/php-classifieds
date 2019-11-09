<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Entity\FeaturedPackage;
use App\Entity\Listing;
use App\Entity\Payment;
use App\Entity\PaymentForFeaturedPackage;
use App\Entity\PaymentForBalanceTopUp;
use App\Entity\User;
use App\Exception\UserVisibleException;
use App\Helper\Random;
use App\Security\CurrentUserService;
use App\Service\Listing\Featured\FeaturedListingService;
use App\Service\Money\UserBalanceService;
use App\Service\Payment\Method\PayPalPaymentMethod;
use App\Service\Payment\Method\PayPalNativePaymentMethod;
use App\Service\Setting\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class PaymentService
{
    /**
     * @var PayPalNativePaymentMethod
     */
    private $paymentMethodService;

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
    /**
     * @var UserBalanceService
     */
    private $userBalanceService;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var FeaturedListingService
     */
    private $featuredListingService;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(
        PayPalPaymentMethod $paymentMethodService,
        UserBalanceService $userBalanceService,
        FeaturedListingService $featuredListingService,
        SettingsService $settingsService,
        CurrentUserService $currentUserService,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface $trans,
        LoggerInterface $logger
    ) {
        $this->paymentMethodService = $paymentMethodService;
        $this->em = $em;
        $this->settingsService = $settingsService;
        $this->currentUserService = $currentUserService;
        $this->trans = $trans;
        $this->userBalanceService = $userBalanceService;
        $this->logger = $logger;
        $this->featuredListingService = $featuredListingService;
        $this->urlGenerator = $urlGenerator;
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
        $paymentDto->setPaymentAppToken(Random::string(64));
        $this->paymentMethodService->createPayment($paymentDto);

        $paymentEntity = new Payment();
        $paymentEntity->setCanceled(false);
        $paymentEntity->setBalanceUpdated(false);
        $paymentEntity->setDatetime(new \DateTime());
        $paymentEntity->setAmount($paymentDto->getAmount());
        $paymentEntity->setGatewayPaymentId($paymentDto->getGatewayPaymentId());
        $paymentEntity->setGatewayToken($paymentDto->getGatewayToken());
        $paymentEntity->setGatewayStatus($paymentDto->getGatewayStatus());
        $paymentEntity->setAppToken($paymentDto->getPaymentAppToken());
        $paymentEntity->setUser($paymentDto->getUser());
        $paymentEntity->setType($paymentDto->getPaymentType());
        $paymentEntity->setDescription($paymentDto->getPaymentDescription());

        $this->em->persist($paymentEntity);

        $paymentDto->setPaymentEntity($paymentEntity);

        return $paymentDto;
    }

    public function markBalanceUpdated(ConfirmPaymentDto $confirmPaymentDto): void
    {
        $paymentEntity = $this->getPaymentEntity($confirmPaymentDto->getPaymentEntity()->getAppToken());
        $paymentEntity->setBalanceUpdated(true);
        $paymentEntity->setGatewayStatus($confirmPaymentDto->getGatewayStatus());

        $this->em->persist($paymentEntity);
    }

    public function isBalanceUpdated(ConfirmPaymentDto $confirmPaymentDto): bool
    {
        return $this->getPaymentEntity($confirmPaymentDto->getPaymentEntity()->getAppToken())->getBalanceUpdated();
    }

    public function confirmPayment(ConfirmPaymentConfigDto $confirmPaymentConfigDto): ConfirmPaymentDto
    {
        $paymentEntity = $this->getPaymentEntity($confirmPaymentConfigDto->getPaymentAppToken());
        $confirmPaymentConfigDto->setPaymentEntity($paymentEntity);
        $confirmPaymentDto = $this->paymentMethodService->confirmPayment($confirmPaymentConfigDto);

        $paymentEntity->setGatewayTransactionId($confirmPaymentDto->getGatewayTransactionId());

        $confirmPaymentDto->setPaymentEntity($paymentEntity);

        return $confirmPaymentDto;
    }

    public function completePurchase(ConfirmPaymentDto $confirmPaymentDto): CompletePurchaseDto
    {
        $completePaymentDto = new CompletePurchaseDto();
        $paymentEntity = $confirmPaymentDto->getPaymentEntity();

        if (!$paymentEntity instanceof Payment) {
            $this->logger->error('could not find payment entity', [$confirmPaymentDto]);

            $this->throwGeneralException();
        }

        $paymentForFeaturedPackage = $paymentEntity->getPaymentForFeaturedPackage();
        if ($paymentForFeaturedPackage instanceof PaymentForFeaturedPackage) {
            $this->markBalanceUpdated($confirmPaymentDto);
            $userBalanceChange = $this->userBalanceService->addBalance(
                $confirmPaymentDto->getGatewayAmount(),
                $paymentForFeaturedPackage->getListing()->getUser(),
                $paymentEntity
            );
            $userBalanceChange->setDescription(
                $this->trans->trans(
                    'trans.Featuring of listing: %listingTitle%, using package: %featuredPackageName%, payment acceptance',
                    [
                        '%listingTitle%' => $paymentForFeaturedPackage->getListing()->getTitle(),
                        '%featuredPackageName%' => $paymentForFeaturedPackage->getFeaturedPackage()->getName(),
                    ]
                )
            );
            $userBalanceChange->setPayment($paymentEntity);
            $this->em->flush();

            $userBalanceChange = $this->featuredListingService->makeFeaturedByBalance(
                $paymentForFeaturedPackage->getListing(),
                $paymentForFeaturedPackage->getFeaturedPackage(),
                $paymentEntity
            );
            $userBalanceChange->setDescription(
                $this->trans->trans(
                    'trans.Featuring of listing: %listingTitle%, using package: %featuredPackageName%',
                    [
                        '%listingTitle%' => $paymentForFeaturedPackage->getListing()->getTitle(),
                        '%featuredPackageName%' => $paymentForFeaturedPackage->getFeaturedPackage()->getName(),
                    ]
                )
            );

            $this->em->flush();
            $this->em->commit();

            $completePaymentDto->setIsRedirect(true);
            $completePaymentDto->setResponse(new RedirectResponse($this->urlGenerator->generate('app_user_feature_listing', [
                'id' => $paymentForFeaturedPackage->getListing()->getId()
            ])));

            return $completePaymentDto;
        }

        if ($paymentEntity->getPaymentForBalanceTopUp() instanceof PaymentForBalanceTopUp) {
            $this->markBalanceUpdated($confirmPaymentDto);
            $userBalanceChange = $this->userBalanceService->addBalance(
                $confirmPaymentDto->getGatewayAmount(),
                $paymentEntity->getPaymentForBalanceTopUp()->getUser(),
                $paymentEntity
            );
            $userBalanceChange->setPayment($paymentEntity);
            $userBalanceChange->setDescription($this->trans->trans('trans.Topping up the account balance'));
            $this->em->flush();
            $this->em->commit();

            $completePaymentDto->setIsRedirect(true);
            $completePaymentDto->setResponse(new RedirectResponse($this->urlGenerator->generate('app_user_balance_top_up')));

            return $completePaymentDto;
        }
    }

    public function getPaymentEntity(string $paymentAppToken): Payment
    {
        return $this->em->getRepository(Payment::class)->findOneBy([
            'appToken' => $paymentAppToken
        ]);
    }

    /**
     * @throws UserVisibleException
     */
    private function throwGeneralException(): void
    {
        throw new UserVisibleException('trans.Could not process payment, if you have been charged and did not receive service, please contact us');
    }
}
