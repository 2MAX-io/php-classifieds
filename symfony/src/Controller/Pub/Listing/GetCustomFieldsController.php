<?php

declare(strict_types=1);

namespace App\Controller\Pub\Listing;

use App\Service\Listing\CustomField\CustomFieldsForListingFormService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetCustomFieldsController extends AbstractController
{
    /**
     * @Route("/listing/get-custom-fields", name="app_listing_get_custom_fields")
     */
    public function getCustomFields(CustomFieldsForListingFormService $customFieldsForListingFormService): Response
    {
        return $this->render('listing/get_custom_fields.html.twig', [
            'customFieldList' => $customFieldsForListingFormService->getFields(),
        ]);
    }
}

