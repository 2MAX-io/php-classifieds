<?php

namespace App\Controller\Pub\User\Listing;

use App\Entity\Listing;
use App\Form\ListingType;
use App\Security\CurrentUserService;
use App\Security\LoginUserProgrammaticallyService;
use App\Service\Category\CategoryListService;
use App\Service\Listing\CustomField\CustomFieldsForListingFormService;
use App\Service\Listing\Save\CreateListingService;
use App\Service\Listing\Save\ListingFileUploadService;
use App\Service\Log\PoliceLogIpService;
use App\Service\User\Create\UserCreateService;
use App\Service\User\Listing\UserListingListService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ListingController extends AbstractController
{
    /**
     * @Route("/user/listing/", name="app_listing_index", methods={"GET"})
     */
    public function index(UserListingListService $userListingListService): Response
    {
        return $this->render('listing/index.html.twig', [
            'listings' => $userListingListService->getList(),
        ]);
    }

    /**
     * @Route("/new", name="app_listing_new", methods={"GET","POST"})
     */
    public function new(
        Request $request,
        ListingFileUploadService $listingFileUploadService,
        CurrentUserService $currentUserService,
        UserCreateService $userCreateService,
        LoginUserProgrammaticallyService $loginUserProgrammaticallyService,
        CreateListingService $createListingService,
        CustomFieldsForListingFormService $customFieldsForListingFormService,
        PoliceLogIpService $logIpService,
        CategoryListService $categoryListService
    ): Response {
        $listing = $createListingService->create();
        $form = $this->createForm(ListingType::class, $listing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('file')->getData()) {
                $listingFileUploadService->addMultipleFilesFromUpload($listing, $form->get('file')->getData());
            }

            if ($request->request->get('fileuploader-list-file')) {
                $listingFileUploadService->updateSort($listing, \json_decode($request->request->get('fileuploader-list-file'), true));
            }
            $customFieldsForListingFormService->saveCustomFieldsToListing($listing, $request->request->get('form_custom_field'));

            if ($currentUserService->getUser()) {
                $listing->setUser($currentUserService->getUser());
            } else {
                $user = $userCreateService->registerUser($listing->getEmail());
                $listing->setUser($user);
                $loginUserProgrammaticallyService->loginUser($user);
                $user->eraseCredentials();
            }

            $createListingService->setFormDependent($listing, $form);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($listing);
            $entityManager->flush();

            $logIpService->saveLog($listing);
            $entityManager->flush();

            return $this->redirectToRoute('listing_edit', ['id' => $listing->getId()]);
        }

        return $this->render('listing/new.html.twig', [
            'listing' => $listing,
            'formCategorySelectList' => $categoryListService->foo(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/listing/{id}/edit", name="listing_edit", methods={"GET","POST"})
     */
    public function edit(
        Request $request,
        Listing $listing,
        CustomFieldsForListingFormService $customFieldsForListingFormService,
        ListingFileUploadService $listingFileUploadService,
        CurrentUserService $currentUserService,
        CreateListingService $createListingService,
        PoliceLogIpService $logIpService
    ): Response {
        if ($currentUserService->getUser() !== $listing->getUser()) {
            throw new UnauthorizedHttpException('user of listing do not match current user');
        }

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
            $customFieldsForListingFormService->saveCustomFieldsToListing($listing, $request->request->get('form_custom_field'));

            $createListingService->setFormDependent($listing, $form);
            $logIpService->saveLog($listing);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('listing_edit', [
                'id' => $listing->getId(),
            ]);
        }

        return $this->render('listing/edit.html.twig', [
            'listing' => $listing,
            'form' => $form->createView(),
            'listingFilesForJavascript' => $createListingService->getListingFilesForJavascript($listing),
        ]);
    }

    /**
     * @Route("/user/listing/{id}", name="app_listing_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Listing $listing, CurrentUserService $currentUserService): Response
    {
        if ($currentUserService->getUser() !== $listing->getUser()) {
            throw new UnauthorizedHttpException('user of listing do not match current user');
        }

        if ($this->isCsrfTokenValid('delete'.$listing->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $listing->setUserRemoved(true);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_listing_index');
    }

    /**
     * @Route("/user/listing/deactivate/{id}", name="app_listing_deactivate", methods={"PATCH"})
     */
    public function deactivate(Request $request, Listing $listing, CurrentUserService $currentUserService): Response
    {
        if ($currentUserService->getUser() !== $listing->getUser()) {
            throw new UnauthorizedHttpException('user of listing do not match current user');
        }

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
        if ($currentUserService->getUser() !== $listing->getUser()) {
            throw new UnauthorizedHttpException('user of listing do not match current user');
        }

        if ($this->isCsrfTokenValid('activate'.$listing->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $listing->setUserDeactivated(false);
            $entityManager->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
