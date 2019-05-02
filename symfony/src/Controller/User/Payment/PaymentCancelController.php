<?php

declare(strict_types=1);

namespace App\Controller\User\Payment;

use App\Entity\Payment;
use App\Service\FlashBag\FlashService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentCancelController extends AbstractController
{
    /**
     * @Route("/user/payment/cancel", name="app_payment_cancel")
     */
    public function paymentCancel(
        Request $request,
        FlashService $flashService,
        EntityManagerInterface $em
    ): Response {
        $gatewayToken = $request->get('token');
        $paymentEntity = $em->getRepository(Payment::class)->findOneBy(['gatewayToken' => $gatewayToken]);

        if (!$paymentEntity) {
            throw $this->createNotFoundException();
        }

        $paymentEntity->setCanceled(true);
        $em->flush();

        $flashService->addFlash(
            FlashService::ERROR_ABOVE_FORM,
            'trans.You have canceled payment. If you want to feature listing please try again and complete payment.'
        );

        return $this->redirectToRoute(
            'app_user_feature_listing',
            ['id' => $paymentEntity->getPaymentFeaturedPackage()->getListing()->getId()]
        );
    }
}
