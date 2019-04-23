<?php

declare(strict_types=1);

namespace App\Controller\Pub\User\Listing;

use App\Controller\Pub\User\Base\AbstractUserController;
use App\Entity\Listing;
use App\Security\CurrentUserService;
use App\Service\Listing\CustomField\CustomFieldsForListingFormService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetCustomFieldsController extends AbstractUserController
{
    /**
     * @Route("/listing/get-custom-fields", options={"expose"=true}, name="app_listing_get_custom_fields")
     */
    public function getCustomFields(Request $request, CustomFieldsForListingFormService $customFieldsForListingFormService, CurrentUserService $currentUserService): Response
    {
        $listingId = $request->query->get('listingId', null);
        if ($listingId) {
            $listing = $this->getDoctrine()->getRepository(Listing::class)->find($listingId);

            if (!$currentUserService->lowSecurityCheckIsAdminInPublic()) {
                $this->dennyUnlessCurrentUserAllowed($listing);
            }
        }

        $categoryId = $request->query->get('categoryId', null);

        return $this->render('user/listing/get_custom_fields.html.twig', [
            'customFieldList' => $customFieldsForListingFormService->getFields((int) $categoryId, (int) $listingId),
        ]);
    }
}

