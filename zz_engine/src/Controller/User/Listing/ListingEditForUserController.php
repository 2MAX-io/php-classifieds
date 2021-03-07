<?php

declare(strict_types=1);

namespace App\Controller\User\Listing;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\Listing;
use App\Enum\ParamEnum;
use App\Form\ListingType;
use App\Service\Category\CategoryListService;
use App\Service\Listing\CustomField\ListingCustomFieldsService;
use App\Service\Listing\Save\ListingFileUploadService;
use App\Service\Listing\Save\SaveListingService;
use App\Service\Listing\Secondary\PoliceLog\PoliceLogForListingService;
use App\Service\Setting\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingEditForUserController extends AbstractUserController
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
     * @Route("/user/listing/new", name="app_listing_new", methods={"GET","POST"})
     */
    public function new(
        Request $request,
        SaveListingService $saveListingService,
        ListingCustomFieldsService $listingCustomFieldsService,
        ListingFileUploadService $listingFileService,
        CategoryListService $categoryListService,
        PoliceLogForListingService $policeLogForListingService,
        SettingsService $settingsService
    ): Response {
        $this->dennyUnlessUser();

        $listingSaveDto = $saveListingService->getListingSaveDtoFromRequest($request);
        $listing = $listingSaveDto->getListing();
        $form = $this->createForm(ListingType::class, $listing);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $listingCustomFieldsService->saveCustomFieldsToListing($listingSaveDto);
            $saveListingService->modifyListingPostFormSubmit($listing, $form);
            $this->em->persist($listing);
            $this->em->flush();

            $listingFileService->saveUploadedFiles($listingSaveDto);
            $policeLogForListingService->saveLog($listing);
            $saveListingService->saveSearchText($listing);
            $saveListingService->saveCustomFieldsInline($listing);
            $this->em->flush();

            return $this->redirectToRoute('app_user_listing_edit', ['id' => $listing->getId()]);
        }

        return $this->render(
            'user/listing/new.html.twig',
            [
                'listing' => $listing,
                'selectCategoryList' => $categoryListService->getCategoryListForSelect(),
                ParamEnum::DATA_FOR_JS => [
                    ParamEnum::LISTING_FILES => $listingFileService->getListingFilesForFrontend($listingSaveDto),
                    ParamEnum::LISTING_ID => $listing->getId(),
                    ParamEnum::MAP_DEFAULT_LATITUDE => $settingsService->getSettingsDto()->getMapDefaultLatitude(),
                    ParamEnum::MAP_DEFAULT_LONGITUDE => $settingsService->getSettingsDto()->getMapDefaultLongitude(),
                    ParamEnum::MAP_DEFAULT_ZOOM => $settingsService->getSettingsDto()->getMapDefaultZoom(),
                ],
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/user/listing/{id}/edit", name="app_user_listing_edit", methods={"GET","POST"})
     */
    public function edit(
        Request $request,
        Listing $listing,
        SaveListingService $saveListingService,
        ListingCustomFieldsService $listingCustomFieldsService,
        ListingFileUploadService $listingFileService,
        CategoryListService $categoryListService,
        PoliceLogForListingService $policeLogForListingService,
        SettingsService $settingsService
    ): Response {
        $this->dennyUnlessCurrentUserAllowed($listing);

        $listingSaveDto = $saveListingService->getListingSaveDtoFromRequest($request, $listing);
        $form = $this->createForm(ListingType::class, $listing);
        $form->remove('validityTimeDays');
        if ($listing->isFeaturedActive()) {
            $form->remove('category');
        }
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $listingCustomFieldsService->saveCustomFieldsToListing($listingSaveDto);
            $listingFileService->saveUploadedFiles($listingSaveDto);
            $saveListingService->modifyListingPostFormSubmit($listing, $form);
            $policeLogForListingService->saveLog($listing);
            $this->em->flush();

            return $this->redirectToRoute('app_user_listing_edit', [
                'id' => $listing->getId(),
            ]);
        }

        return $this->render(
            'user/listing/edit.html.twig',
            [
                'listing' => $listing,
                'selectCategoryList' => $categoryListService->getCategoryListForSelect(),
                ParamEnum::DATA_FOR_JS => [
                    ParamEnum::LISTING_FILES => $listingFileService->getListingFilesForFrontend($listingSaveDto),
                    ParamEnum::LISTING_ID => $listing->getId(),
                    ParamEnum::MAP_DEFAULT_LATITUDE => $settingsService->getSettingsDto()->getMapDefaultLatitude(),
                    ParamEnum::MAP_DEFAULT_LONGITUDE => $settingsService->getSettingsDto()->getMapDefaultLongitude(),
                    ParamEnum::MAP_DEFAULT_ZOOM => $settingsService->getSettingsDto()->getMapDefaultZoom(),
                    ParamEnum::MAP_LOCATION_COORDINATES => [
                        ParamEnum::LATITUDE => $listing->getLocationLatitude(),
                        ParamEnum::LONGITUDE => $listing->getLocationLongitude(),
                    ],
                ],
                'form' => $form->createView(),
            ]
        );
    }
}
