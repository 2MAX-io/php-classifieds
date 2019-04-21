<?php

namespace App\Controller\Pub\User\Listing;

use App\Controller\Pub\User\Base\AbstractUserController;
use App\Entity\Listing;
use App\Form\ListingType;
use App\Security\CurrentUserService;
use App\Service\Category\CategoryListService;
use App\Service\Listing\CustomField\CustomFieldsForListingFormService;
use App\Service\Listing\Save\CreateListingService;
use App\Service\Listing\Save\ListingFileUploadService;
use App\Service\Log\PoliceLogIpService;
use App\Service\System\Pagination\PaginationService;
use App\Service\User\Listing\UserListingListService;
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
    public function index(Request $request, UserListingListService $userListingListService, PaginationService $paginationService): Response
    {
        $userListingListDto = $userListingListService->getList((int) $request->get('page', 1));

        return $this->render('user/listing/index.html.twig', [
            'listings' => $userListingListDto->getResults(),
            'pagination' => $paginationService->getPaginationHtml($userListingListDto->getPager()),
            'pager' => $userListingListDto->getPager(),
        ]);
    }

    /**
     * @Route("/user/listing/new", name="app_listing_new", methods={"GET","POST"})
     */
    public function new(
        Request $request,
        ListingFileUploadService $listingFileUploadService,
        CurrentUserService $currentUserService,
        CreateListingService $createListingService,
        CustomFieldsForListingFormService $customFieldsForListingFormService,
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
            $customFieldsForListingFormService->saveCustomFieldsToListing($listing, $request->request->get('form_custom_field', []));

            $listing->setUser($currentUserService->getUser());
            $createListingService->setFormDependent($listing, $form);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($listing);
            $entityManager->flush();

            if ($form->get('file')->getData()) {
                $listingFileUploadService->addMultipleFilesFromUpload($listing, $form->get('file')->getData());
            }

            if ($request->request->get('fileuploader-list-file')) {
                $listingFileUploadService->updateSort($listing, \json_decode($request->request->get('fileuploader-list-file'), true));
            }

            $logIpService->saveLog($listing);
            $entityManager->flush();

            return $this->redirectToRoute('app_listing_edit', ['id' => $listing->getId()]);
        }

        return $this->render('user/listing/new.html.twig', [
            'listing' => $listing,
            'formCategorySelectList' => $categoryListService->getFormCategorySelectList(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/listing/{id}/edit", name="app_listing_edit", methods={"GET","POST"})
     */
    public function edit(
        Request $request,
        Listing $listing,
        CustomFieldsForListingFormService $customFieldsForListingFormService,
        ListingFileUploadService $listingFileUploadService,
        CreateListingService $createListingService,
        PoliceLogIpService $logIpService,
        CategoryListService $categoryListService
    ): Response {
        $this->dennyUnlessCurrentUserListing($listing);

        $form = $this->createForm(ListingType::class, $listing);
        $form->remove('validityTimeDays');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('file')->getData()) {
                $listingFileUploadService->addMultipleFilesFromUpload($listing, $form->get('file')->getData());
            }

            if ($request->request->get('fileuploader-list-file')) {
                $listingFileUploadService->updateSort($listing, \json_decode($request->request->get('fileuploader-list-file'), true));
            }
            $customFieldsForListingFormService->saveCustomFieldsToListing($listing, $request->request->get('form_custom_field', []));

            $createListingService->setFormDependent($listing, $form);
            $logIpService->saveLog($listing);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_listing_edit', [
                'id' => $listing->getId(),
            ]);
        }

        return $this->render('user/listing/edit.html.twig', [
            'listing' => $listing,
            'form' => $form->createView(),
            'listingFilesForJavascript' => $createListingService->getListingFilesForJavascript($listing),
            'formCategorySelectList' => $categoryListService->getFormCategorySelectList(),
        ]);
    }

    /**
     * @Route("/user/listing/{id}", name="app_listing_remove", methods={"DELETE"})
     */
    public function remove(Request $request, Listing $listing, CurrentUserService $currentUserService): Response
    {
        $this->dennyUnlessCurrentUserListing($listing);

        if ($this->isCsrfTokenValid('remove'.$listing->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $listing->setUserRemoved(true);
            $entityManager->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/user/listing/deactivate/{id}", name="app_listing_deactivate", methods={"PATCH"})
     */
    public function deactivate(Request $request, Listing $listing, CurrentUserService $currentUserService): Response
    {
        $this->dennyUnlessCurrentUserListing($listing);

        if ($this->isCsrfTokenValid('deactivate'.$listing->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $listing->setUserDeactivated(true);
            $entityManager->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/user/listing/activate/{id}", name="app_listing_activate", methods={"PATCH"})
     */
    public function activate(Request $request, Listing $listing, CurrentUserService $currentUserService): Response
    {
        $this->dennyUnlessCurrentUserListing($listing);

        if ($this->isCsrfTokenValid('activate'.$listing->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $listing->setUserDeactivated(false);
            $entityManager->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
