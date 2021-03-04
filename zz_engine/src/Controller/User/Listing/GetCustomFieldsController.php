<?php

declare(strict_types=1);

namespace App\Controller\User\Listing;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\Category;
use App\Entity\Listing;
use App\Form\ListingCustomFieldsType;
use App\Form\ListingType;
use App\Security\CurrentUserService;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetCustomFieldsController extends AbstractUserController
{
    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    public function __construct(CurrentUserService $currentUserService, FormFactoryInterface $formFactory)
    {
        $this->currentUserService = $currentUserService;
        $this->formFactory = $formFactory;
    }

    /**
     * @Route("/listing/get-custom-fields", name="app_listing_get_custom_fields", options={"expose"=true})
     */
    public function getCustomFields(Request $request): Response
    {
        if ($request->query->has('listingId')) {
            $listingId = $request->query->get('listingId');
            $listing = $this->getDoctrine()->getRepository(Listing::class)->find($listingId);
            if (null === $listing) {
                throw new \UnexpectedValueException('listing not found by ID');
            }
        } else {
            $listing = new Listing();
        }

        $this->dennyIfNotAllowed($listing);

        $categoryId = $request->query->get('categoryId');
        $category = $this->getDoctrine()->getRepository(Category::class)->find($categoryId);
        if ($category) {
            $listing->setCategory($category);
        }

        return $this->render('user/listing/other/get_custom_fields.html.twig', [
            'form' => $this->getForm($listing)->createView(),
        ]);
    }

    private function getForm(Listing $listing): FormInterface
    {
        $formBuilder = $this->formFactory->createNamedBuilder(
            ListingType::LISTING_FIELD,
            FormType::class,
            null,
            [
                'csrf_protection' => false,
            ],
        );
        $formBuilder->add(
            ListingCustomFieldsType::CUSTOM_FIELD_LIST_FIELD,
            ListingCustomFieldsType::class,
            [
                'listingEntity' => $listing,
            ],
        );

        return $formBuilder->getForm();
    }

    private function dennyIfNotAllowed(Listing $listing): void
    {
        if ($this->currentUserService->isAdminInPublic()) {
            return; // skip checking admin
        }

        $newListing = null === $listing->getId();

        if ($newListing) {
            $this->dennyUnlessUser();
        } else {
            $this->dennyUnlessCurrentUserAllowed($listing);
        }
    }
}
