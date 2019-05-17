<?php

declare(strict_types=1);

namespace App\Controller\User\Listing;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\Category;
use App\Entity\Listing;
use App\Form\ListingCustomFieldListType;
use App\Form\ListingType;
use App\Security\CurrentUserService;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetCustomFieldsController extends AbstractUserController
{
    /**
     * @Route("/listing/get-custom-fields", name="app_listing_get_custom_fields", options={"expose"=true})
     */
    public function getCustomFields(
        Request $request,
        CurrentUserService $currentUserService,
        FormFactoryInterface $formFactory
    ): Response {
        $listing = null;
        $listingId = $request->query->get('listingId', null);
        if ($listingId) {
            $listing = $this->getDoctrine()->getRepository(Listing::class)->find($listingId);

            if (!$currentUserService->lowSecurityCheckIsAdminInPublic()) {
                $this->dennyUnlessCurrentUserAllowed($listing);
            }
        }
        if (empty($listing)) {
            $listing = new Listing();
        }

        $categoryId = $request->query->get('categoryId', null);
        $category = $this->getDoctrine()->getRepository(Category::class)->find($categoryId);
        if ($category) {
            $listing->setCategory($category);
        }

        $formBuilder = $formFactory->createNamedBuilder(
            ListingType::LISTING_FIELD,
            FormType::class,
            null,
            [
                'csrf_protection' => false,
            ]
        );
        $formBuilder->add(
            ListingCustomFieldListType::CUSTOM_FIELD_LIST_FIELD,
            ListingCustomFieldListType::class,
            [
                'listingEntity' => $listing,

            ]
        );

        return $this->render(
            'user/listing/get_custom_fields.html.twig',
            [
                'form' => $formBuilder->getForm()->createView(),
            ]
        );
    }
}

