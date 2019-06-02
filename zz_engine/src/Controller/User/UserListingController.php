<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\Listing;
use App\Form\ListingCustomFieldListType;
use App\Form\ListingType;
use App\Security\CurrentUserService;
use App\Service\Category\CategoryListService;
use App\Service\Event\FileModificationEventService;
use App\Service\Listing\CustomField\ListingCustomFieldsService;
use App\Service\Listing\Save\SaveListingService;
use App\Service\Listing\Save\ListingFileUploadService;
use App\Service\Log\PoliceLogIpService;
use App\Service\User\Listing\UserListingListService;
use Minwork\Helper\Arr;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserListingController extends AbstractUserController
{
    /**
     * @var TranslatorInterface
     */
    private $trans;

    public function __construct(TranslatorInterface $trans)
    {
        $this->trans = $trans;
    }

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
        ListingFileUploadService $listingFileUploadService,
        CurrentUserService $currentUserService,
        SaveListingService $createListingService,
        ListingCustomFieldsService $listingCustomFieldsService,
        PoliceLogIpService $logIpService,
        CategoryListService $categoryListService
    ): Response {
        $this->dennyUnlessUser();

        $listing = $createListingService->create();
        if ($currentUserService->getUser()) {
            $listing->setEmail($currentUserService->getUser()->getEmail());
        }
        $form = $this->createForm(ListingType::class, $listing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $listingCustomFieldsService->saveCustomFieldsToListing(
                $listing,
                Arr::getNestedElement(
                    $request->request->all(),
                    [ListingType::LISTING_FIELD, ListingCustomFieldListType::CUSTOM_FIELD_LIST_FIELD]
                ) ?? [] // listing[customFieldList]
            );

            $listing->setUser($currentUserService->getUser());
            $createListingService->setFormDependent($listing, $form);

            $em = $this->getDoctrine()->getManager();
            $em->persist($listing);
            $em->flush();

            if ($form->get('file')->getData()) {
                $listingFileUploadService->addMultipleFilesFromUpload(
                    $listing,
                    $form->get('file')->getData()
                );
            }

            if ($request->request->get('fileuploader-list-file')) {
                $listingFileUploadService->updateSort(
                    $listing,
                    \json_decode($request->request->get('fileuploader-list-file'), true)
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
                'formCategorySelectList' => $categoryListService->getFormCategorySelectList(),
                'form' => $form->createView(),
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
        ListingFileUploadService $listingFileUploadService,
        SaveListingService $createListingService,
        PoliceLogIpService $logIpService,
        CategoryListService $categoryListService
    ): Response {
        $this->dennyUnlessCurrentUserAllowed($listing);

        $form = $this->createForm(ListingType::class, $listing);
        $form->remove('validityTimeDays');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('file')->getData()) {
                $listingFileUploadService->addMultipleFilesFromUpload(
                    $listing,
                    $form->get('file')->getData()
                );
            }

            if ($request->request->get('fileuploader-list-file')) {
                $listingFileUploadService->updateSort(
                    $listing,
                    \json_decode($request->request->get('fileuploader-list-file'), true)
                );
            }
            $listingCustomFieldsService->saveCustomFieldsToListing(
                $listing,
                Arr::getNestedElement(
                    $request->request->all(),
                    [ListingType::LISTING_FIELD, ListingCustomFieldListType::CUSTOM_FIELD_LIST_FIELD]
                ) ?? [] // listing[customFieldList]
            );

            $createListingService->setFormDependent($listing, $form);
            $logIpService->saveLog($listing);
            $this->getDoctrine()->getManager()->flush();

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
                'listingFilesForJavascript' => $createListingService->getListingFilesForJavascript($listing),
                'formCategorySelectList' => $categoryListService->getFormCategorySelectList(),
            ]
        );
    }

    /**
     * @Route("/user/listing/{id}", name="app_listing_remove", methods={"DELETE"})
     */
    public function remove(
        Request $request,
        Listing $listing,
        FileModificationEventService $fileModificationEventService
    ): Response {
        $this->dennyUnlessCurrentUserAllowed($listing, true);

        if ($this->isCsrfTokenValid('remove' . $listing->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $listing->setUserRemoved(true);
            $fileModificationEventService->onFileModificationByListing($listing);
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
