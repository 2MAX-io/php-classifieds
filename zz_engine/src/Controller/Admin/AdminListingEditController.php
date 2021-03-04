<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\Listing;
use App\Enum\ParamEnum;
use App\Form\Admin\AdminListingEditType;
use App\Service\Listing\CustomField\ListingCustomFieldsService;
use App\Service\Listing\Save\SaveListingService;
use App\Service\Setting\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminListingEditController extends AbstractAdminController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/admin/red5/listing/edit/{id}", name="app_admin_listing_edit")
     */
    public function listingEditForAdmin(
        Request $request,
        Listing $listing,
        ListingCustomFieldsService $listingCustomFieldsService,
        SaveListingService $saveListingService,
        SettingsService $settingsService
    ): Response {
        $this->denyUnlessAdmin();

        $listingSaveDto = $saveListingService->getListingSaveDtoFromRequest($request, $listing);
        $form = $this->createForm(AdminListingEditType::class, $listing);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $listingCustomFieldsService->saveCustomFieldsToListing($listingSaveDto);
            $saveListingService->saveSearchText($listing);
            $saveListingService->updateSlug($listing);
            $this->em->flush();

            return $this->redirectToRoute('app_admin_listing_edit', [
                'id' => $listing->getId(),
            ]);
        }

        return $this->render('admin/listing/edit/admin_listing_edit.html.twig', [
            'form' => $form->createView(),
            'listing' => $listing,
            ParamEnum::DATA_FOR_JS => [
                ParamEnum::LISTING_ID => $listing->getId(),
                ParamEnum::MAP_LOCATION_COORDINATES => [
                    ParamEnum::LATITUDE => $listing->getLocationLatitude(),
                    ParamEnum::LONGITUDE => $listing->getLocationLongitude(),
                ],
                ParamEnum::MAP_DEFAULT_LATITUDE => $settingsService->getSettingsDto()->getMapDefaultLatitude(),
                ParamEnum::MAP_DEFAULT_LONGITUDE => $settingsService->getSettingsDto()->getMapDefaultLongitude(),
                ParamEnum::MAP_DEFAULT_ZOOM => $settingsService->getSettingsDto()->getMapDefaultZoom(),
            ],
        ]);
    }
}
