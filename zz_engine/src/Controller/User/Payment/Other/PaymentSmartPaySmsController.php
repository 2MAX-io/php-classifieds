<?php

declare(strict_types=1);

namespace App\Controller\User\Payment\Other;

use App\Entity\Listing;
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
     * @Route("/payment/smartpay/{smartpayPlUrlSecret}", name="app_payment_smartpay_sms")
     */
    public function paymentSmartPaySms(
        string $smartpayPlUrlSecret,
        Request $request,
        FeaturedListingService $featuredListingService,
        LoggerInterface $logger,
        EntityManagerInterface $em
    ): Response {
        if (!isset($_ENV['APP_SMARTPAY_PL_URL_SECRET'])) {
            $logger->critical('ENV variable APP_SMARTPAY_PL_URL_SECRET not found');

            return new Response('ENV variable APP_SMARTPAY_PL_URL_SECRET not found');
        }

        if ($smartpayPlUrlSecret !== $_ENV['APP_SMARTPAY_PL_URL_SECRET']) {
            $logger->critical('ENV variable APP_SMARTPAY_PL_URL_SECRET not found');

            return new Response('{smartpayPlUrlSecret} does not match APP_SMARTPAY_PL_URL_SECRET');
        }

        try {
            $smsText = \trim($request->get('text'));
            $smsPaymentNumber = \trim((string) $request->get('number'));
            $listingId = \preg_replace('~\D+~', '', $smsText);
            /** @var array<string,int> $numberToDaysMap */
            $numberToDaysMap = [];
            $numberToDaysMap['71068'] = 3;
            $numberToDaysMap['72068'] = 7;
            $numberToDaysMap['74068'] = 21;

            $featuredTimeDays = $numberToDaysMap[$smsPaymentNumber];
            /** @var null|Listing $listing */
            $listing = $em->getRepository(Listing::class)->find($listingId);
            if (null === $listing) {
                $logger->alert('[Featured Listing] IMPORTANT! listing not found, SMS text: {smsText}', [
                    'smsText' => $smsText,
                ]);

                return new Response('OK');
            }

            $featuredListingService->makeFeatured($listing, $featuredTimeDays * 3600 * 24);
            $em->flush();
        } catch (\Throwable $e) {
            $logger->alert('error while featuring listing by SMS', ExceptionHelper::flatten($e));

            return new Response('FAIL');
        }

        return new Response('OK');
    }
}
