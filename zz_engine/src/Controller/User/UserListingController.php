<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\Listing;
use App\Form\ListingType;
use App\Helper\Json;
use App\Service\Category\CategoryListService;
use App\Service\Listing\CustomField\ListingCustomFieldsService;
use App\Service\Listing\Save\SaveListingService;
use App\Service\Listing\Save\ListingFileService;
use App\Service\Log\PoliceLogIpService;
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
            'user/listing/index.html.twig',
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
        ListingFileService $listingFileService,
        SaveListingService $createListingService,
        ListingCustomFieldsService $listingCustomFieldsService,
        PoliceLogIpService $logIpService,
        CategoryListService $categoryListService,
        EntityManagerInterface $em
    ): Response {
        $this->dennyUnlessUser();

        $listing = $createListingService->createListingForForm();
        $form = $this->createForm(ListingType::class, $listing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $listingCustomFieldsService->saveCustomFieldsToListing(
                $listing,
                $createListingService->getCustomFieldValueListArrayFromRequest($request)
            );
            $createListingService->setListingProperties($listing, $form);

            $em->persist($listing);
            $em->flush();
            if ($request->request->get('fileuploader-list-files')) {
                $listingFileService->processListingFiles(
                    $listing,
                    Json::decodeToArray($request->request->get('fileuploader-list-files')) ?? []
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
                'listingFilesForJavascript' => $createListingService->getListingFilesForJavascript($listing),
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
        ListingFileService $listingFileService,
        SaveListingService $createListingService,
        PoliceLogIpService $logIpService,
        CategoryListService $categoryListService,
        EntityManagerInterface $em
    ): Response {
        $this->dennyUnlessCurrentUserAllowed($listing);

        $form = $this->createForm(ListingType::class, $listing);
        $form->remove('validityTimeDays');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->request->get('fileuploader-list-files')) {
                $listingFileService->processListingFiles(
                    $listing,
                    Json::decodeToArray($request->request->get('fileuploader-list-files')) ?? []
                );
            }
            $listingCustomFieldsService->saveCustomFieldsToListing(
                $listing,
                $createListingService->getCustomFieldValueListArrayFromRequest($request)
            );
            $createListingService->setListingProperties($listing, $form);
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
                'listingFilesForJavascript' => $createListingService->getListingFilesForJavascript($listing),
            ]
        );
    }

    /**
     * @Route("/user/listing/{id}", name="app_listing_remove", methods={"DELETE"})
     */
    public function remove(
        Request $request,
        Listing $listing
    ): Response {
        $this->dennyUnlessCurrentUserAllowed($listing, true);

        if ($this->isCsrfTokenValid('remove' . $listing->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $listing->setUserRemoved(true);
            $em->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/user/listing/deactivate/{id}", name="app_listing_deactivate", methods={"PATCH"})
     */
    public function deactivate(Request $request, Listing $listing): Response
    {
        $this->dennyUnlessCurrentUserAllowed($listing);

        if ($this->isCsrfTokenValid('deactivate' . $listing->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $listing->setUserDeactivated(true);
            $em->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/user/listing/activate/{id}", name="app_listing_activate", methods={"PATCH"})
     */
    public function activate(Request $request, Listing $listing): Response
    {
        $this->dennyUnlessCurrentUserAllowed($listing);

        if ($this->isCsrfTokenValid('activate' . $listing->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $listing->setUserDeactivated(false);
            $em->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
