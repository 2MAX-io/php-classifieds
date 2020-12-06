<?php

declare(strict_types=1);

namespace App\Controller\User\Listing;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\Listing;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeatureListingBySmsController extends AbstractUserController
{
    /**
     * @Route("/user/feature/send-sms/{id}", name="app_user_feature_listing_by_sms")
     */
    public function featureBySms(
        Listing $listing
    ): Response {
        $this->dennyUnlessCurrentUserAllowed($listing);

        return $this->render('user/listing/feature_listing_by_sms.twig', [
            'listing' => $listing,
        ]);
    }
}
