<?php

declare(strict_types=1);

namespace App\Controller\User\Payment;

use App\Entity\Payment;
use App\Entity\PaymentForBalanceTopUp;
use App\Entity\PaymentForFeaturedPackage;
use App\Exception\UserVisibleException;
use App\Helper\ExceptionHelper;
use App\Service\Listing\Featured\FeaturedListingService;
use App\Service\Money\UserBalanceService;
use App\Service\Payment\ConfirmPaymentConfigDto;
use App\Service\Payment\PaymentService;
use App\Service\Setting\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class PaymentController extends AbstractController
{
    /**
     * @Route("/user/payment/{paymentAppToken}", name="app_payment")
     */
    public function payment(
        Request $request,
        string $paymentAppToken,
        PaymentService $paymentService,
        UserBalanceService $userBalanceService,
        FeaturedListingService $featuredListingService,
        SettingsService $settingsService,
        EntityManagerInterface $em,
        TranslatorInterface $trans,
        LoggerInterface $logger
    ): Response {
        if (!$settingsService->getSettingsDto()->isPaymentAllowed()) {
            throw new UserVisibleException('trans.Payments have been disabled');
        }

        $em->beginTransaction();

        try {
            $confirmPaymentConfigDto = new ConfirmPaymentConfigDto();
            $confirmPaymentConfigDto->setRequest($request);
            $confirmPaymentConfigDto->setPaymentAppToken($paymentAppToken);
            $confirmPaymentDto = $paymentService->confirmPayment($confirmPaymentConfigDto);

            if (!$confirmPaymentDto->isConfirmed()) {
                $logger->error('payment is not confirmed', [$confirmPaymentDto]);

                $this->throwGeneralException();
            }

            if ($paymentService->isBalanceUpdated($confirmPaymentDto)) {
                $logger->error('balance has been already updated', [$confirmPaymentDto]);

                $this->throwGeneralException();
            }

            $paymentEntity = $confirmPaymentDto->getPaymentEntity();
            if (!$paymentEntity instanceof Payment) {
                $logger->error('could not find payment entity', [$confirmPaymentDto]);

                $this->throwGeneralException();
            }

            if ($confirmPaymentDto->getGatewayAmount() !== $paymentEntity->getAmount()) {
                $logger->error('paid amount do not match between gateway and payment entity', [$confirmPaymentDto]);

                $this->throwGeneralException();
            }

            $paymentForFeaturedPackage = $paymentEntity->getPaymentForFeaturedPackage();
            if ($paymentForFeaturedPackage instanceof PaymentForFeaturedPackage) {
                $paymentService->markBalanceUpdated($confirmPaymentDto);
                $userBalanceChange = $userBalanceService->addBalance(
                    $confirmPaymentDto->getGatewayAmount(),
                    $paymentForFeaturedPackage->getListing()->getUser(),
                    $paymentEntity
                );
                $userBalanceChange->setDescription(
                    $trans->trans(
                        'trans.Featuring of listing: %listingTitle%, using package: %featuredPackageName%, payment acceptance',
                        [
                            '%listingTitle%' => $paymentForFeaturedPackage->getListing()->getTitle(),
                            '%featuredPackageName%' => $paymentForFeaturedPackage->getFeaturedPackage()->getName(),
                        ]
                    )
                );
                $userBalanceChange->setPayment($paymentEntity);
                $em->flush();

                $userBalanceChange = $featuredListingService->makeFeaturedByBalance(
                    $paymentForFeaturedPackage->getListing(),
                    $paymentForFeaturedPackage->getFeaturedPackage(),
                    $paymentEntity
                );
                $userBalanceChange->setDescription(
                    $trans->trans(
                        'trans.Featuring of listing: %listingTitle%, using package: %featuredPackageName%',
                        [
                            '%listingTitle%' => $paymentForFeaturedPackage->getListing()->getTitle(),
                            '%featuredPackageName%' => $paymentForFeaturedPackage->getFeaturedPackage()->getName(),
                        ]
                    )
                );

                $em->flush();
                $em->commit();

                return $this->redirectToRoute(
                    'app_user_feature_listing',
                    ['id' => $paymentForFeaturedPackage->getListing()->getId()]
                );
            }

            if ($paymentEntity->getPaymentForBalanceTopUp() instanceof PaymentForBalanceTopUp) {
                $paymentService->markBalanceUpdated($confirmPaymentDto);
                $userBalanceChange = $userBalanceService->addBalance(
                    $confirmPaymentDto->getGatewayAmount(),
                    $paymentEntity->getPaymentForBalanceTopUp()->getUser(),
                    $paymentEntity
                );
                $userBalanceChange->setPayment($paymentEntity);
                $userBalanceChange->setDescription($trans->trans('trans.Topping up the account balance'));
                $em->flush();
                $em->commit();

                return $this->redirectToRoute('app_user_balance_top_up');
            }


            if ($em->getConnection()->isTransactionActive()) {
                $em->commit();
            }

        } catch (\Throwable $e) {
            $em->rollback();

            $logger->error('error while processing payment', ExceptionHelper::flatten($e));

            $this->throwGeneralException();
        }

        $this->throwGeneralException();

        return new Response();
    }

    /**
     * @throws UserVisibleException
     */
    private function throwGeneralException(): void
    {
        throw new UserVisibleException('trans.Could not process payment, if you have been charged and did not receive service, please contact us');
    }
}
