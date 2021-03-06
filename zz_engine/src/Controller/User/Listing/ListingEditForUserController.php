<?php

declare(strict_types=1);

namespace App\Controller\User\Listing;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\Listing;
use App\Enum\ParamEnum;
use App\Form\Listing\ListingType;
use App\Service\Category\CategoryListService;
use App\Service\Listing\CustomField\ListingCustomFieldsService;
use App\Service\Listing\Save\ListingFileUploadService;
use App\Service\Listing\Save\SaveListingService;
use App\Service\Listing\Secondary\PoliceLog\PoliceLogForListingService;
use App\Service\Setting\SettingsDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class ListingEditForUserController extends AbstractUserController
{
    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager, EntityManagerInterface $em)
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->em = $em;
    }

    /**
     * @Route("/user/listing/new", name="app_user_listing_new", methods={"GET","POST"})
     */
    public function new(
        Request $request,
        SaveListingService $saveListingService,
        ListingCustomFieldsService $listingCustomFieldsService,
        ListingFileUploadService $listingFileService,
        CategoryListService $categoryListService,
        PoliceLogForListingService $policeLogForListingService,
        SettingsDto $settingsDto
    ): Response {
        $this->dennyUnlessUser();

        $listingSaveDto = $saveListingService->getListingSaveDtoFromRequest($request);
        $listing = $listingSaveDto->getListing();
        $form = $this->createForm(ListingType::class, $listing);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $listingSaveDto->setPackage($form->get(ListingType::PACKAGE_FIELD)->getData());
            $listingCustomFieldsService->saveCustomFieldsToListing($listingSaveDto);
            $saveListingService->modifyListingPostFormSubmit($listingSaveDto, $form);
            $this->em->persist($listing);
            $this->em->flush();

            $listingFileService->saveUploadedFiles($listingSaveDto);
            $policeLogForListingService->saveLog($listing);
            $saveListingService->saveSearchText($listing);
            $saveListingService->saveCustomFieldsInline($listing);
            $this->em->flush();

            if ($listingSaveDto->getPackage() && $listingSaveDto->getPackage()->isPaidPackage()) {
                return $this->redirectToRoute('app_user_feature_listing_pay', [
                    'id' => $listing->getId(),
                    'package' => $listingSaveDto->getPackageNotNull()->getId(),
                    '_token' => $this->csrfTokenManager->getToken('csrf_feature'.$listing->getId())->getValue(),
                ]);
            }

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
                    ParamEnum::MAP_DEFAULT_LATITUDE => $settingsDto->getMapDefaultLatitude(),
                    ParamEnum::MAP_DEFAULT_LONGITUDE => $settingsDto->getMapDefaultLongitude(),
                    ParamEnum::MAP_DEFAULT_ZOOM => $settingsDto->getMapDefaultZoom(),
                    ParamEnum::CSRF_TOKEN => $this->csrfTokenManager->getToken('csrf_listingFileRemove')->getValue(),
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
        SettingsDto $settingsDto
    ): Response {
        $this->dennyUnlessCurrentUserAllowed($listing);

        $listingSaveDto = $saveListingService->getListingSaveDtoFromRequest($request, $listing);
        $form = $this->createForm(ListingType::class, $listing);
        if (!$listing->isExpired() && !$listing->getUserDeactivated()) {
            $form->remove('validityTimeDays');
        }
        if ($listing->isFeaturedActive()) {
            $form->remove('category');
        }
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $listingSaveDto->setPackage($form->get(ListingType::PACKAGE_FIELD)->getData());
            $listingCustomFieldsService->saveCustomFieldsToListing($listingSaveDto);
            $listingFileService->saveUploadedFiles($listingSaveDto);
            $saveListingService->modifyListingPostFormSubmit($listingSaveDto, $form);
            $saveListingService->saveCustomFieldsInline($listing);
            $policeLogForListingService->saveLog($listing);
            $this->em->flush();

            if ($listingSaveDto->getPackage() && $listingSaveDto->getPackage()->isPaidPackage()) {
                return $this->redirectToRoute('app_user_feature_listing_pay', [
                    'id' => $listing->getId(),
                    'package' => $listingSaveDto->getPackageNotNull()->getId(),
                    '_token' => $this->csrfTokenManager->getToken('csrf_feature'.$listing->getId())->getValue(),
                ]);
            }

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
                    ParamEnum::MAP_DEFAULT_LATITUDE => $settingsDto->getMapDefaultLatitude(),
                    ParamEnum::MAP_DEFAULT_LONGITUDE => $settingsDto->getMapDefaultLongitude(),
                    ParamEnum::MAP_DEFAULT_ZOOM => $settingsDto->getMapDefaultZoom(),
                    ParamEnum::MAP_LOCATION_COORDINATES => [
                        ParamEnum::LATITUDE => $listing->getLocationLatitude(),
                        ParamEnum::LONGITUDE => $listing->getLocationLongitude(),
                    ],
                    ParamEnum::CSRF_TOKEN => $this->csrfTokenManager->getToken('csrf_listingFileRemove')->getValue(),
                ],
                'form' => $form->createView(),
            ]
        );
    }
}
