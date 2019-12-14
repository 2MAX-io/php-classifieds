<?php

declare(strict_types=1);

namespace App\Controller\User\Payment\Other;

use App\Entity\Listing;
use App\Exception\UserVisibleException;
use App\Helper\ExceptionHelper;
use App\Service\Listing\Featured\FeaturedListingService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * smartpay.pl / Mobiltek SMS payment success confirmation controller
 */
class PaymentSmartPaySmsController extends AbstractController
{
    /**
     * @Route("/payment/smartpay/Lufn4twHDxDzzfHfiFzKL", name="app_payment_smartpay_sms")
     */
    public function paymentSmartPaySms(
        Request $request,
        FeaturedListingService $featuredListingService,
        LoggerInterface $logger,
        EntityManagerInterface $em
    ): Response {
        try {
            $smsText = \trim($request->get('text'));
            $smsPaymentNumber = (string) \trim($request->get('number'));
            $listingId = \preg_replace('~\D+~', '', $smsText);
            $numberToDaysMap = [];
            $numberToDaysMap['71068'] = 7;
            $numberToDaysMap['72068'] = 21;
            $numberToDaysMap['74068'] = 42;

            $featuredTimeDays = $numberToDaysMap[$smsPaymentNumber];
            /** @var Listing $listing */
            $listing = $em->getRepository(Listing::class)->find($listingId);
            if (null === $listing) {
                $logger->alert('listing not found');
                throw new UserVisibleException('Listing not found');
            }

            $featuredListingService->makeFeatured($listing, $featuredTimeDays * 3600*24);
            $em->flush();
        } catch (\Throwable $e) {
            $logger->alert('error while featuring listing by SMS', ExceptionHelper::flatten($e));

            return new Response('FAIL');
        }

        return new Response('OK');
    }
}
