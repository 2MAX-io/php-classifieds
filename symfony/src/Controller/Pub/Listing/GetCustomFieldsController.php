<?php

declare(strict_types=1);

namespace App\Controller\Pub\Listing;

use App\Service\Listing\CustomField\CustomFieldsForListingFormService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetCustomFieldsController extends AbstractController
{
    /**
     * @Route("/listing/get-custom-fields", name="app_listing_get_custom_fields")
     */
    public function getCustomFields(Request $request, CustomFieldsForListingFormService $customFieldsForListingFormService): Response
    {
        $listingId = $request->query->get('listingId', null);
        $categoryId = $request->query->get('categoryId', null);

        return $this->render('listing/get_custom_fields.html.twig', [
            'customFieldList' => $customFieldsForListingFormService->getFields((int) $categoryId, (int) $listingId),
        ]);
    }
}

