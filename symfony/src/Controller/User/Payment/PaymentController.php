<?php

declare(strict_types=1);

namespace App\Controller\User\Payment;

use App\Entity\User;
use App\Service\Listing\Featured\FeaturedListingService;
use App\Service\Money\UserBalanceService;
use App\Service\Payment\ConfirmPaymentDto;
use App\Service\Payment\PaymentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    /**
     * @Route("/user/payment", name="app_payment")
     */
    public function index(
        Request $request,
        PaymentService $paymentService,
        UserBalanceService $userBalanceService,
        FeaturedListingService $featuredListingService,
        EntityManagerInterface $em
    ): Response {
        $em->beginTransaction();

        try {
            $confirmPaymentDto = new ConfirmPaymentDto();
            $confirmPaymentDto = $paymentService->confirmPayment($request, $confirmPaymentDto);

            if ($confirmPaymentDto->isConfirmed() && !$paymentService->isBalanceUpdated($confirmPaymentDto)) {
                $paymentEntity = $paymentService->getPaymentEntity($confirmPaymentDto);
                $paymentFeaturedPackage = $paymentEntity->getPaymentFeaturedPackage();
                if ($confirmPaymentDto->getGatewayAmount() !== $paymentEntity->getAmount()) {
                    throw new \UnexpectedValueException('paid amount do not match between gateway and payment entity');
                }

                $paymentService->markBalanceUpdated($confirmPaymentDto);
                $userBalanceService->addBalance(
                    $confirmPaymentDto->getGatewayAmount(),
                    $paymentFeaturedPackage->getListing()->getUser()
                );
                $em->flush();

                $featuredListingService->makeFeaturedByBalance(
                    $paymentFeaturedPackage->getListing(),
                    $paymentFeaturedPackage->getFeaturedPackage()
                );

                $em->flush(); // todo: check if transaction logic with ifs and closing correct
                $em->commit();

                return $this->redirectToRoute(
                    'app_user_feature_listing',
                    ['id' => $paymentFeaturedPackage->getListing()->getId()]
                );
            }

            if ($em->getConnection()->isTransactionActive()) {
                $em->commit();
            }

        } catch (\Throwable $e) {
            $em->rollback();
            throw $e;
        }

        return new Response('ok');
    }
}
