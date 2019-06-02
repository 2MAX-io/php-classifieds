<?php

declare(strict_types=1);

namespace App\Controller\User\Payment;

use App\Exception\UserVisibleMessageException;
use App\Service\Listing\Featured\FeaturedListingService;
use App\Service\Money\UserBalanceService;
use App\Service\Payment\ConfirmPaymentDto;
use App\Service\Payment\PaymentService;
use App\Service\Setting\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class PaymentController extends AbstractController
{
    /**
     * @Route("/user/payment", name="app_payment")
     */
    public function payment(
        Request $request,
        PaymentService $paymentService,
        UserBalanceService $userBalanceService,
        FeaturedListingService $featuredListingService,
        SettingsService $settingsService,
        EntityManagerInterface $em,
        TranslatorInterface $trans
    ): Response {
        if (!$settingsService->getSettingsDto()->isPaymentAllowed()) {
            throw new UserVisibleMessageException('trans.Payments have been disabled');
        }

        $em->beginTransaction();

        try {
            $confirmPaymentDto = new ConfirmPaymentDto();
            $confirmPaymentDto = $paymentService->confirmPayment($request, $confirmPaymentDto);

            if ($confirmPaymentDto->isConfirmed() && !$paymentService->isBalanceUpdated($confirmPaymentDto)) {
                $paymentEntity = $confirmPaymentDto->getPaymentEntity();

                if ($confirmPaymentDto->getGatewayAmount() !== $paymentEntity->getAmount()) {
                    throw new \UnexpectedValueException('paid amount do not match between gateway and payment entity');
                }

                if ($paymentEntity->getPaymentForFeaturedPackage()) {
                    $paymentForFeaturedPackage = $paymentEntity->getPaymentForFeaturedPackage();

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

                    $em->flush(); // todo: check if transaction logic with ifs and closing correct
                    $em->commit();

                    return $this->redirectToRoute(
                        'app_user_feature_listing',
                        ['id' => $paymentForFeaturedPackage->getListing()->getId()]
                    );
                }

                if ($paymentEntity->getPaymentForBalanceTopUp()) {
                    $paymentService->markBalanceUpdated($confirmPaymentDto);
                    $userBalanceChange = $userBalanceService->addBalance(
                        $confirmPaymentDto->getGatewayAmount(),
                        $paymentEntity->getPaymentForBalanceTopUp()->getUser(),
                        $paymentEntity
                    );
                    $userBalanceChange->setPayment($paymentEntity);
                    $userBalanceChange->setDescription($trans->trans('trans.Topping up the account balance'));
                    $em->flush(); // todo: check if transaction logic with ifs and closing correct
                    $em->commit();

                    return $this->redirectToRoute('app_user_balance_top_up');
                }
            }

            if ($em->getConnection()->isTransactionActive()) {
                $em->commit();
            }

        } catch (\Throwable $e) {
            $em->rollback();
            throw $e;
        }

        throw new UserVisibleMessageException('trans.Could not process payment, if you have been charged and did not receive service, please contact us');
    }
}
