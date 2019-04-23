<?php

declare(strict_types=1);

namespace App\Controller\Pub\User\Listing;

use App\Controller\Pub\User\Base\AbstractUserController;
use App\Entity\Listing;
use App\Service\Listing\Featured\FeaturedListingService;
use Symfony\Component\HttpFoundation\Request;
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
            'listing' => $listing,
        ]);
    }

    /**
     * @Route("/user/feature/make-featured/as-demo/{id}", name="app_user_feature_listing_as_demo", methods={"PATCH"})
     */
    public function featureAsDemo(
        Request $request,
        Listing $listing,
        FeaturedListingService $featuredListingService
    ): Response {
        $this->dennyUnlessCurrentUserAllowed($listing);

        if ($this->isCsrfTokenValid('featureAsDemo'.$listing->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $featuredListingService->makeFeaturedAsDemo($listing);
            $entityManager->flush();

            return $this->redirectToRoute(
                'app_user_feature_listing',
                ['id' => $listing->getId(), 'demoStarted' => 1]
            );
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
