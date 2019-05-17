<?php

declare(strict_types=1);

namespace App\Controller\User\Listing;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\Category;
use App\Entity\Listing;
use App\Form\ListingCustomFieldListType;
use App\Security\CurrentUserService;
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
        CurrentUserService $currentUserService
    ): Response {
        $listingId = $request->query->get('listingId', null);
        if ($listingId) {
            $listing = $this->getDoctrine()->getRepository(Listing::class)->find($listingId);

            if (!$currentUserService->lowSecurityCheckIsAdminInPublic()) {
                $this->dennyUnlessCurrentUserAllowed($listing);
            }
        }

        $categoryId = $request->query->get('categoryId', null);
        $category = $this->getDoctrine()->getRepository(Category::class)->find($categoryId);

        if ($category && !empty($listing)) {
            $listing->setCategory($category);
        }

        $form = $this->createForm(ListingCustomFieldListType::class, [], [
            'listingEntity' => $listing ?? null,
        ]);

        return $this->render(
            'user/listing/get_custom_fields.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}

