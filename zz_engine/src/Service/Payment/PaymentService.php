<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Entity\FeaturedPackage;
use App\Entity\Listing;
use App\Entity\Payment;
use App\Entity\PaymentForBalanceTopUp;
use App\Entity\PaymentForFeaturedPackage;
use App\Entity\User;
use App\Helper\DateHelper;
use App\Helper\RandomHelper;
use App\Security\CurrentUserService;
use App\Service\Invoice\CreateInvoiceService;
use App\Service\Listing\Featured\FeaturedListingService;
use App\Service\Money\UserBalanceService;
use App\Service\Payment\Dto\CompletePurchaseDto;
use App\Service\Payment\Dto\ConfirmPaymentConfigDto;
use App\Service\Payment\Dto\ConfirmPaymentDto;
use App\Service\Payment\Dto\PaymentDto;
use App\Service\Setting\SettingsDto;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class PaymentService
{
    /**
     * @var PaymentGatewayService
     */
    private $paymentGatewayService;

    /**
     * @var UserBalanceService
     */
    private $userBalanceService;

    /**
     * @var CreateInvoiceService
     */
    private $createInvoiceService;

    /**
     * @var FeaturedListingService
     */
    private $featuredListingService;

    /**
     * @var SettingsDto
     */
    private $settingsDto;

    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var TranslatorInterface
     */
    private $trans;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        PaymentGatewayService $paymentGatewayService,
        UserBalanceService $userBalanceService,
        CreateInvoiceService $createInvoiceService,
        FeaturedListingService $featuredListingService,
        SettingsDto $settingsDto,
        CurrentUserService $currentUserService,
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface $trans,
        EntityManagerInterface $em,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->settingsDto = $settingsDto;
        $this->currentUserService = $currentUserService;
        $this->trans = $trans;
        $this->userBalanceService = $userBalanceService;
        $this->logger = $logger;
        $this->featuredListingService = $featuredListingService;
        $this->urlGenerator = $urlGenerator;
        $this->paymentGatewayService = $paymentGatewayService;
        $this->createInvoiceService = $createInvoiceService;
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
        $paymentDto->setCurrency($this->settingsDto->getCurrency());
        $paymentDto->setAmount($featuredPackage->getPrice());
        $paymentDto->setUser($this->currentUserService->getUserOrNull());
        if ($this->settingsDto->getPaymentGatewayPaymentDescription()) {
            $paymentDto->setGatewayPaymentDescription($this->settingsDto->getPaymentGatewayPaymentDescription());
        } else {
            $paymentDto->setGatewayPaymentDescription($this->trans->trans('trans.Promotion of listings'));
        }

        $paymentDto = $this->createPayment($paymentDto);

        $paymentForFeaturedPackage = new PaymentForFeaturedPackage();
        $paymentForFeaturedPackage->setPayment($paymentDto->getPaymentEntity());
        $paymentForFeaturedPackage->setFeaturedPackage($featuredPackage);
        $paymentForFeaturedPackage->setListing($listing);
        $this->em->persist($paymentForFeaturedPackage);

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
        $paymentDto->setCurrency($this->settingsDto->getCurrency());
        $paymentDto->setAmount($amount);
        $paymentDto->setUser($this->currentUserService->getUserOrNull());
        if ($this->settingsDto->getPaymentGatewayPaymentDescription()) {
            $paymentDto->setGatewayPaymentDescription($this->settingsDto->getPaymentGatewayPaymentDescription());
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
        $paymentDto->setPaymentAppToken(RandomHelper::string(64));
        $paymentGateway = $this->paymentGatewayService->getPaymentGateway();
        $paymentGateway->createPayment($paymentDto);

        $paymentEntity = new Payment();
        $paymentEntity->setCanceled(false);
        $paymentEntity->setPaid(false);
        $paymentEntity->setDelivered(false);
        $paymentEntity->setDatetime(DateHelper::create());
        $paymentEntity->setAmount($paymentDto->getAmount());
        $paymentEntity->setCurrency($paymentDto->getCurrency());
        $paymentEntity->setGatewayPaymentId($paymentDto->getGatewayPaymentId());
        $paymentEntity->setGatewayStatus($paymentDto->getGatewayStatus());
        $paymentEntity->setGatewayName($paymentGateway::getName());
        $paymentEntity->setGatewayMode($paymentGateway->getGatewayMode());
        $paymentEntity->setAppPaymentToken($paymentDto->getPaymentAppToken());
        $paymentEntity->setUser($paymentDto->getUser());
        $paymentEntity->setType($paymentDto->getPaymentType());
        $paymentEntity->setDescription($paymentDto->getPaymentDescription());

        $this->em->persist($paymentEntity);

        $paymentDto->setPaymentEntity($paymentEntity);

        return $paymentDto;
    }

    public function confirmPayment(ConfirmPaymentConfigDto $confirmPaymentConfigDto): CompletePurchaseDto
    {
        $paymentEntity = $this->getPaymentEntity($confirmPaymentConfigDto->getPaymentAppToken());
        $confirmPaymentConfigDto->setPaymentEntity($paymentEntity);
        $paymentGateway = $this->paymentGatewayService->getPaymentGateway();
        $confirmPaymentDto = $paymentGateway->confirmPayment($confirmPaymentConfigDto);

        if ($paymentEntity->getGatewayPaymentId() && $paymentEntity->getGatewayPaymentId() !== $confirmPaymentDto->getGatewayPaymentId()) {
            $this->logger->error('transaction id do not match one in payment entity', [
                'paymentEntityGatewayPaymentId' => $paymentEntity->getGatewayPaymentId(),
                'requestGatewayPaymentId' => $confirmPaymentDto->getGatewayPaymentId(),
            ]);

            throw new \RuntimeException('transaction id do not match one in payment entity');
        }
        $paymentEntity->setPaid(true);
        $paymentEntity->setGatewayPaymentId($confirmPaymentDto->getGatewayPaymentId());
        $paymentEntity->setGatewayAmountPaid($confirmPaymentDto->getGatewayAmount());
        $paymentEntity->setGatewayStatus($confirmPaymentDto->getGatewayStatus());
        $confirmPaymentDto->setPaymentEntity($paymentEntity);

        $this->validate($confirmPaymentDto);

        return $this->completePurchase($confirmPaymentDto);
    }

    public function cancelPayment(string $paymentAppToken): ?Payment
    {
        $paymentEntity = $this->em->getRepository(Payment::class)->findOneBy([
            'appToken' => $paymentAppToken,
        ]);
        if (!$paymentEntity) {
            return null;
        }

        $paymentEntity->setCanceled(true);

        return $paymentEntity;
    }

    public function validate(ConfirmPaymentDto $confirmPaymentDto): void
    {
        if (!$confirmPaymentDto->isConfirmed()) {
            $this->logger->error('payment is not confirmed', [$confirmPaymentDto]);

            throw new \RuntimeException('payment is not confirmed');
        }

        if ($this->isDelivered($confirmPaymentDto)) {
            $this->logger->error('already delivered', [$confirmPaymentDto]);

            throw new \RuntimeException('already delivered');
        }

        $paymentEntity = $confirmPaymentDto->getPaymentEntity();
        if (!$paymentEntity instanceof Payment) {
            $this->logger->error('could not find payment entity', [$confirmPaymentDto]);

            throw new \RuntimeException('could not find payment entity');
        }

        if ($confirmPaymentDto->getGatewayAmount() !== $paymentEntity->getAmount()) {
            $this->logger->error('paid amount do not match between gateway and payment entity', [$confirmPaymentDto]);

            throw new \RuntimeException('paid amount do not match between gateway and payment entity');
        }
    }

    public function isDelivered(ConfirmPaymentDto $confirmPaymentDto): bool
    {
        $paymentAppToken = $confirmPaymentDto->getPaymentEntityNotNull()->getAppPaymentToken();

        return $this->getPaymentEntity($paymentAppToken)->getDelivered();
    }

    public function completePurchase(ConfirmPaymentDto $confirmPaymentDto): CompletePurchaseDto
    {
        $completePaymentDto = new CompletePurchaseDto();
        $paymentEntity = $confirmPaymentDto->getPaymentEntity();
        if (!$paymentEntity instanceof Payment) {
            $this->logger->error('could not find payment entity', [$confirmPaymentDto]);

            throw new \RuntimeException('could not find payment entity');
        }

        $paymentForFeaturedPackage = $paymentEntity->getPaymentForFeaturedPackage();
        if ($paymentForFeaturedPackage instanceof PaymentForFeaturedPackage) {
            $userBalanceChange = $this->userBalanceService->addBalance(
                $confirmPaymentDto->getGatewayAmount(),
                $paymentForFeaturedPackage->getListingNotNull()->getUser(),
                $paymentEntity,
            );
            $userBalanceChange->setDescription(
                $this->trans->trans(
                    'trans.Featuring of listing: %listingTitle%, using package: %featuredPackageName%, payment acceptance',
                    [
                        '%listingTitle%' => $paymentForFeaturedPackage->getListingNotNull()->getTitle(),
                        '%featuredPackageName%' => $paymentForFeaturedPackage->getFeaturedPackage()->getName(),
                    ]
                )
            );
            $userBalanceChange->setPayment($paymentEntity);
            $this->em->flush();

            $userBalanceChange = $this->featuredListingService->makeFeaturedByBalance(
                $paymentForFeaturedPackage->getListingNotNull(),
                $paymentForFeaturedPackage->getFeaturedPackage(),
                $paymentEntity
            );
            $userBalanceChange->setDescription(
                $this->trans->trans(
                    'trans.Featuring of listing: %listingTitle%, using package: %featuredPackageName%',
                    [
                        '%listingTitle%' => $paymentForFeaturedPackage->getListingNotNull()->getTitle(),
                        '%featuredPackageName%' => $paymentForFeaturedPackage->getFeaturedPackage()->getName(),
                    ]
                )
            );

            $completePaymentDto->setIsSuccess(true);
            $completePaymentDto->setRedirectResponse(
                new RedirectResponse(
                    $this->urlGenerator->generate(
                        'app_user_feature_listing',
                        [
                            'id' => $paymentForFeaturedPackage->getListingNotNull()->getId(),
                        ]
                    )
                )
            );
            $this->markDelivered($confirmPaymentDto);

            $this->createInvoiceService->createInvoice($paymentEntity);

            return $completePaymentDto;
        }

        if ($paymentEntity->getPaymentForBalanceTopUp() instanceof PaymentForBalanceTopUp) {
            $userBalanceChange = $this->userBalanceService->addBalance(
                $confirmPaymentDto->getGatewayAmount(),
                $paymentEntity->getPaymentForBalanceTopUp()->getUser(),
                $paymentEntity,
            );
            $userBalanceChange->setPayment($paymentEntity);
            $userBalanceChange->setDescription($this->trans->trans('trans.Topping up the account balance'));

            $completePaymentDto->setIsSuccess(true);
            $completePaymentDto->setRedirectResponse(new RedirectResponse($this->urlGenerator->generate('app_user_balance_top_up')));
            $this->markDelivered($confirmPaymentDto);

            $this->createInvoiceService->createInvoice($paymentEntity);

            return $completePaymentDto;
        }

        return $completePaymentDto;
    }

    public function markDelivered(ConfirmPaymentDto $confirmPaymentDto): void
    {
        $paymentAppToken = $confirmPaymentDto->getPaymentEntityNotNull()->getAppPaymentToken();
        $paymentEntity = $this->getPaymentEntity($paymentAppToken);
        $paymentEntity->setDelivered(true);

        $this->em->persist($paymentEntity);
    }

    public function getPaymentEntity(string $paymentAppToken): Payment
    {
        $payment = $this->em->getRepository(Payment::class)->findOneBy([
            'appPaymentToken' => $paymentAppToken,
        ]);
        if (null === $payment) {
            throw new \RuntimeException("payment not found by: `{$paymentAppToken}`");
        }

        return $payment;
    }
}
