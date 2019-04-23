<?php

declare(strict_types=1);

namespace App\Controller\Pub\User\Listing;

use App\Controller\Pub\User\Base\AbstractUserController;
use App\Entity\Listing;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeatureListingController extends AbstractUserController
{
    /**
     * @Route("/user/feature/{id}", name="app_user_feature_listing")
     */
    public function feature(Listing $listing): Response
    {
        $this->dennyUnlessCurrentUserAllowed($listing);

        return $this->render('user/listing/featured_extend.html.twig', [
        ]);
    }
}
