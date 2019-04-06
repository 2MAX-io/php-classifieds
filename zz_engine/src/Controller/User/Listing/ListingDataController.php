<?php

declare(strict_types=1);

namespace App\Controller\User\Listing;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\Category;
use App\Entity\Listing;
use App\Enum\ParamEnum;
use App\Form\Listing\ListingCustomFieldsType;
use App\Form\Listing\ListingType;
use App\Form\Listing\SelectPackageType;
use App\Security\CurrentUserService;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ListingDataController extends AbstractUserController
{
    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    public function __construct(
        CurrentUserService $currentUserService,
        FormFactoryInterface $formFactory
    ) {
        $this->currentUserService = $currentUserService;
        $this->formFactory = $formFactory;
    }

    /**
     * @Route("/listing/listing-data", name="app_listing_data",
     *     methods={"POST"},
     *     options={"expose"=true},
     * )
     */
    public function listingData(Request $request, Environment $twig): Response
    {
        if ($request->query->has(ParamEnum::LISTING_ID)) {
            $listingId = $request->query->get(ParamEnum::LISTING_ID);
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

        return $this->json([
            ParamEnum::CUSTOM_FIELD => $twig->render('user/listing/form/get_custom_fields.html.twig', [
                'form' => $this->getCustomFieldsForm($listing)->createView(),
            ]),
            ParamEnum::PACKAGE_LIST => $twig->render('user/listing/form/packages_for_listing_category.html.twig', [
                'form' => $this->getPackagesForm($listing)->createView(),
            ]),
        ]);
    }

    private function getCustomFieldsForm(Listing $listing): FormInterface
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

    private function getPackagesForm(Listing $listing): FormInterface
    {
        $formBuilder = $this->formFactory->createNamedBuilder(
            ListingType::LISTING_FIELD,
            FormType::class,
            null,
            [
                'csrf_protection' => false,
            ],
        );
        $formBuilder->add(ListingType::PACKAGE_FIELD, SelectPackageType::class, [
            'required' => $listing->isExpired(),
            'category' => $listing->getCategory(),
        ]);

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
