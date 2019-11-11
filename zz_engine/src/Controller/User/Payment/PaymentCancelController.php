<?php

declare(strict_types=1);

namespace App\Controller\User\Payment;

use App\Entity\Payment;
use App\Service\FlashBag\FlashService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentCancelController extends AbstractController
{
    /**
     * @Route("/user/payment/cancel/{paymentAppToken}", name="app_payment_cancel")
     */
    public function paymentCancel(
        string $paymentAppToken,
        FlashService $flashService,
        EntityManagerInterface $em
    ): Response {
        $paymentEntity = $em->getRepository(Payment::class)->findOneBy(['appToken' => $paymentAppToken]);
        if (!$paymentEntity) {
            throw $this->createNotFoundException();
        }

        $paymentEntity->setCanceled(true);
        $em->flush();

        if ($paymentEntity->getPaymentForFeaturedPackage()) {
            $flashService->addFlash(
                FlashService::ERROR_ABOVE_FORM,
                'trans.You have canceled payment. If you want to feature listing, try again and complete payment'
            );

            return $this->redirectToRoute(
                'app_user_feature_listing',
                ['id' => $paymentEntity->getPaymentForFeaturedPackage()->getListing()->getId()]
            );
        }

        if ($paymentEntity->getPaymentForBalanceTopUp()) {
            $flashService->addFlash(
                FlashService::ERROR_ABOVE_FORM,
                'trans.You have canceled payment. If you want to top up account, try again and complete payment'
            );

            return $this->redirectToRoute('app_user_balance_top_up');
        }

        throw $this->createNotFoundException();
    }
}
