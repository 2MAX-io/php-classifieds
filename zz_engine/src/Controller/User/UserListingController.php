<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\Listing;
use App\Enum\ParamEnum;
use App\Form\ListingType;
use App\Helper\Json;
use App\Service\Category\CategoryListService;
use App\Service\Listing\CustomField\ListingCustomFieldsService;
use App\Service\Listing\Save\SaveListingService;
use App\Service\Listing\Save\ListingFileUploadService;
use App\Service\Log\PoliceLogIpService;
use App\Service\System\Routing\RefererService;
use App\Service\User\Listing\UserListingListService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserListingController extends AbstractUserController
{
    /**
     * @Route("/user/listing/", name="app_user_listing_index", methods={"GET"})
     */
    public function index(
        Request $request,
        UserListingListService $userListingListService
    ): Response {
        $this->dennyUnlessUser();

        $userListingListDto = $userListingListService->getList((int)$request->get('page', 1));

        return $this->render(
            'user/listing/user_listings_list.twig',
            [
                'listings' => $userListingListDto->getResults(),
                'pager' => $userListingListDto->getPager(),
            ]
        );
    }

    /**
     * @Route("/user/listing/new", name="app_listing_new", methods={"GET","POST"})
     */
    public function new(
        Request $request,
        ListingFileUploadService $listingFileService,
        SaveListingService $saveListingService,
        ListingCustomFieldsService $listingCustomFieldsService,
        PoliceLogIpService $logIpService,
        CategoryListService $categoryListService,
        EntityManagerInterface $em
    ): Response {
        $this->dennyUnlessUser();

        $listing = $saveListingService->createListingForForm();
        $form = $this->createForm(ListingType::class, $listing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $listingCustomFieldsService->saveCustomFieldsToListing(
                $listing,
                $saveListingService->getCustomFieldValueListArrayFromRequest($request)
            );
            $saveListingService->modifyListingPostFormSubmit($listing, $form);

            $em->persist($listing);
            $em->flush();
            if ($request->request->get('fileuploader-list-files')) {
                $listingFileService->processListingFiles(
                    $listing,
                    Json::toArray($request->request->get('fileuploader-list-files')) ?? []
                );
            }

            $logIpService->saveLog($listing);
            $em->flush();

            return $this->redirectToRoute('app_listing_edit', ['id' => $listing->getId()]);
        }

        return $this->render(
            'user/listing/new.html.twig',
            [
                'listing' => $listing,
                'form' => $form->createView(),
                'formCategorySelectList' => $categoryListService->getFormCategorySelectList(),
                ParamEnum::JSON_DATA => [
                    'listingFilesForJavascript' => $saveListingService->getListingFilesForJavascript($listing),
                    'listingId' => $listing->getId(),
                ],
            ]
        );
    }

    /**
     * @Route("/user/listing/{id}/edit", name="app_listing_edit", methods={"GET","POST"})
     */
    public function edit(
        Request $request,
        Listing $listing,
        ListingCustomFieldsService $listingCustomFieldsService,
        ListingFileUploadService $listingFileService,
        SaveListingService $saveListingService,
        PoliceLogIpService $logIpService,
        CategoryListService $categoryListService,
        EntityManagerInterface $em
    ): Response {
        $this->dennyUnlessCurrentUserAllowed($listing);

        $form = $this->createForm(ListingType::class, $listing);
        $form->remove('validityTimeDays');
        if ($listing->isFeaturedActive()) {
            $form->remove('category');
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->request->get('fileuploader-list-files')) {
                $listingFileService->processListingFiles(
                    $listing,
                    Json::toArray($request->request->get('fileuploader-list-files')) ?? []
                );
            }
            $listingCustomFieldsService->saveCustomFieldsToListing(
                $listing,
                $saveListingService->getCustomFieldValueListArrayFromRequest($request)
            );
            $saveListingService->modifyListingPostFormSubmit($listing, $form);
            $logIpService->saveLog($listing);

            $em->flush();

            return $this->redirectToRoute(
                'app_listing_edit',
                [
                    'id' => $listing->getId(),
                ]
            );
        }

        return $this->render(
            'user/listing/edit.html.twig',
            [
                'listing' => $listing,
                'form' => $form->createView(),
                'formCategorySelectList' => $categoryListService->getFormCategorySelectList(),
                ParamEnum::JSON_DATA => [
                    'listingFilesForJavascript' => $saveListingService->getListingFilesForJavascript($listing),
                    'listingId' => $listing->getId(),
                ],
            ]
        );
    }

    /**
     * @Route("/user/listing/{id}", name="app_listing_remove", methods={"DELETE"})
     */
    public function remove(
        Request $request,
        Listing $listing,
        RefererService $refererService
    ): Response {
        $this->dennyUnlessCurrentUserAllowed($listing, true);

        if ($this->isCsrfTokenValid('remove' . $listing->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $listing->setUserRemoved(true);
            $em->flush();
        }

        if ($refererService->refererIsRoute('app_listing_edit')) {
            return $this->redirectToRoute('app_user_listing_index');
        }

        return $refererService->redirectToRefererResponse();
    }

    /**
     * @Route("/user/listing/deactivate/{id}", name="app_listing_deactivate", methods={"PATCH"})
     */
    public function deactivate(Request $request, Listing $listing, RefererService $refererService): Response
    {
        $this->dennyUnlessCurrentUserAllowed($listing);

        if ($this->isCsrfTokenValid('deactivate' . $listing->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $listing->setUserDeactivated(true);
            $em->flush();
        }

        return $refererService->redirectToRefererResponse();
    }

    /**
     * @Route("/user/listing/activate/{id}", name="app_listing_activate", methods={"PATCH"})
     */
    public function activate(Request $request, Listing $listing, RefererService $refererService): Response
    {
        $this->dennyUnlessCurrentUserAllowed($listing);

        if ($this->isCsrfTokenValid('activate' . $listing->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $listing->setUserDeactivated(false);
            $em->flush();
        }

        /**
         * after activation, listing could be expired, for better ux experience, in that case redirect to
         * listing validity extend controller
         */
        if ($listing->getStatus() === $listing::STATUS_EXPIRED) {
            return $this->redirectToRoute('app_user_validity_extend', ['id' => $listing->getId()]);
        }

        return $refererService->redirectToRefererResponse();
    }
}
