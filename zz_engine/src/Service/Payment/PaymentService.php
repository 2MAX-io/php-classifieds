<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Entity\Listing;
use App\Entity\Package;
use App\Entity\Payment;
use App\Entity\PaymentForBalanceTopUp;
use App\Entity\PaymentForPackage;
use App\Entity\User;
use App\Helper\DateHelper;
use App\Helper\RandomHelper;
use App\Security\CurrentUserService;
use App\Service\Invoice\CreateInvoiceService;
use App\Service\Listing\Featured\FeaturedListingService;
use App\Service\Money\UserBalanceService;
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

    public function createPaymentForPackage(Listing $listing, Package $package): PaymentDto
    {
        $paymentDto = new PaymentDto();
        $paymentDto->setPaymentType(Payment::FOR_PACKAGE_TYPE);
        $paymentDto->setPaymentDescription(
            $this->trans->trans(
                'trans.Payment for featured listing: %listingInfo%, package: %featurePackageName%, price: %price%, featured days: %featuredDays%',
                [
                    '%featurePackageName%' => "{$package->getName()} ({$package->getAdminName()}) [id:{$package->getId()}]",
                    '%price%' => $package->getPriceFloat(),
                    '%listingInfo%' => "{$listing->getTitle()} [id: {$listing->getId()}]",
                    '%featuredDays%' => $package->getDaysFeaturedExpire(),
                ]
            )
        );
        $paymentDto->setCurrency($this->settingsDto->getCurrency());
        $paymentDto->setAmount($package->getPrice());
        $paymentDto->setUser($this->currentUserService->getUserOrNull());
        if ($this->settingsDto->getPaymentGatewayPaymentDescription()) {
            $paymentDto->setGatewayPaymentDescription($this->settingsDto->getPaymentGatewayPaymentDescription());
        } else {
            $paymentDto->setGatewayPaymentDescription($this->trans->trans('trans.Promotion of listings'));
        }
        $paymentDto = $this->createPayment($paymentDto);

        $paymentForPackage = new PaymentForPackage();
        $paymentForPackage->setPayment($paymentDto->getPaymentEntity());
        $paymentForPackage->setPackage($package);
        $paymentForPackage->setListing($listing);
        $this->em->persist($paymentForPackage);

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

    public function confirmPayment(ConfirmPaymentDto $confirmPaymentDto): ConfirmPaymentDto
    {
        $paymentGateway = $this->paymentGatewayService->getPaymentGateway();
        $paymentEntity = $this->getPaymentEntity($confirmPaymentDto->getPaymentAppToken());
        $confirmPaymentDto->setPaymentEntity($paymentEntity);
        $confirmPaymentDto = $paymentGateway->confirmPayment($confirmPaymentDto);
        $this->validate($confirmPaymentDto);

        $paymentEntity->setPaid(true);
        $paymentEntity->setGatewayPaymentId($confirmPaymentDto->getGatewayPaymentId());
        $paymentEntity->setGatewayAmountPaid($confirmPaymentDto->getGatewayAmount());
        $paymentEntity->setGatewayStatus($confirmPaymentDto->getGatewayStatus());

        return $this->completePurchase($confirmPaymentDto);
    }

    public function validate(ConfirmPaymentDto $confirmPaymentDto): void
    {
        if (!$confirmPaymentDto->isConfirmed()) {
            $this->logger->error('payment is not confirmed', [$confirmPaymentDto]);

            throw new \RuntimeException('payment is not confirmed');
        }

        $paymentEntity = $confirmPaymentDto->getPaymentEntity();
        if (!$paymentEntity instanceof Payment) {
            $this->logger->error('could not find payment entity', [$confirmPaymentDto]);

            throw new \RuntimeException('could not find payment entity');
        }

        if ($paymentEntity->getDelivered()) {
            $this->logger->error('already delivered', [$confirmPaymentDto]);

            throw new \RuntimeException('already delivered');
        }

        if ($confirmPaymentDto->getGatewayAmount() !== $paymentEntity->getAmount()) {
            $this->logger->error('paid amount do not match between gateway and payment entity', [$confirmPaymentDto]);

            throw new \RuntimeException('paid amount do not match between gateway and payment entity');
        }

        if ($paymentEntity->getGatewayPaymentId() && $paymentEntity->getGatewayPaymentId() !== $confirmPaymentDto->getGatewayPaymentId()) {
            $this->logger->error('transaction id do not match one in payment entity', [
                'paymentEntityGatewayPaymentId' => $paymentEntity->getGatewayPaymentId(),
                'requestGatewayPaymentId' => $confirmPaymentDto->getGatewayPaymentId(),
            ]);

            throw new \RuntimeException('transaction id do not match one in payment entity');
        }
    }

    public function cancelPayment(string $paymentAppToken): ?Payment
    {
        $paymentEntity = $this->em->getRepository(Payment::class)->findOneBy([
            'appPaymentToken' => $paymentAppToken,
        ]);
        if (!$paymentEntity) {
            return null;
        }

        $paymentEntity->setCanceled(true);

        return $paymentEntity;
    }

    public function completePurchase(ConfirmPaymentDto $confirmPaymentDto): ConfirmPaymentDto
    {
        $paymentEntity = $confirmPaymentDto->getPaymentEntity();
        if (!$paymentEntity instanceof Payment) {
            $this->logger->error('could not find payment entity', [$confirmPaymentDto]);

            throw new \RuntimeException('could not find payment entity');
        }

        $paymentForPackage = $paymentEntity->getPaymentForPackage();
        if ($paymentForPackage instanceof PaymentForPackage) {
            $userBalanceChange = $this->userBalanceService->addBalance(
                $confirmPaymentDto->getGatewayAmount(),
                $paymentForPackage->getListingNotNull()->getUser(),
                $paymentEntity,
            );
            $userBalanceChange->setDescription(
                $this->trans->trans(
                    'trans.Featuring of listing: %listingTitle%, using package: %packageName%, payment acceptance',
                    [
                        '%listingTitle%' => $paymentForPackage->getListingNotNull()->getTitle(),
                        '%packageName%' => $paymentForPackage->getPackage()->getName(),
                    ]
                )
            );
            $userBalanceChange->setPayment($paymentEntity);
            $this->em->flush();

            $userBalanceChange = $this->featuredListingService->makeFeaturedByBalance(
                $paymentForPackage->getListingNotNull(),
                $paymentForPackage->getPackage(),
                $paymentEntity,
            );
            $userBalanceChange->setDescription(
                $this->trans->trans(
                    'trans.Featuring of listing: %listingTitle%, using package: %packageName%',
                    [
                        '%listingTitle%' => $paymentForPackage->getListingNotNull()->getTitle(),
                        '%packageName%' => $paymentForPackage->getPackage()->getName(),
                    ]
                )
            );

            $confirmPaymentDto->setSuccess(true);
            $confirmPaymentDto->setRedirectResponse(
                new RedirectResponse(
                    $this->urlGenerator->generate(
                        'app_user_feature_listing',
                        [
                            'id' => $paymentForPackage->getListingNotNull()->getId(),
                        ]
                    )
                )
            );

            $this->markDelivered($confirmPaymentDto);
            $this->createInvoiceService->createInvoice($paymentEntity);

            return $confirmPaymentDto;
        }

        if ($paymentEntity->getPaymentForBalanceTopUp() instanceof PaymentForBalanceTopUp) {
            $userBalanceChange = $this->userBalanceService->addBalance(
                $confirmPaymentDto->getGatewayAmount(),
                $paymentEntity->getPaymentForBalanceTopUp()->getUser(),
                $paymentEntity,
            );
            $userBalanceChange->setPayment($paymentEntity);
            $userBalanceChange->setDescription($this->trans->trans('trans.Topping up the account balance'));

            $confirmPaymentDto->setSuccess(true);
            $confirmPaymentDto->setRedirectResponse(
                new RedirectResponse($this->urlGenerator->generate('app_user_balance_top_up')),
            );

            $this->markDelivered($confirmPaymentDto);
            $this->createInvoiceService->createInvoice($paymentEntity);

            return $confirmPaymentDto;
        }

        return $confirmPaymentDto;
    }

    public function markDelivered(ConfirmPaymentDto $confirmPaymentDto): void
    {
        $paymentEntity = $confirmPaymentDto->getPaymentEntityNotNull();
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
