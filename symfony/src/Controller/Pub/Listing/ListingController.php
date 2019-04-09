<?php

namespace App\Controller\Pub\Listing;

use App\Entity\Listing;
use App\Form\ListingType;
use App\Security\CurrentUserService;
use App\Service\Listing\CustomField\CustomFieldsForListingFormService;
use App\Service\Listing\Save\ListingFileUploadService;
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
        CurrentUserService $currentUserService
    ): Response {
        $listing = new Listing();
        $form = $this->createForm(ListingType::class, $listing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('file')->getData()) {
                $listingFileUploadService->addBannerFileFromUpload($listing, $form->get('file')->getData());
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($listing);
            $entityManager->flush();

            return $this->redirectToRoute('app_listing_index');
        }

        return $this->render('listing/new.html.twig', [
            'listing' => $listing,
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
        CurrentUserService $currentUserService
    ): Response {
        if ($currentUserService->getUser() !== $listing->getUser()) {
            throw new UnauthorizedHttpException('user of listing do not match current user');
        }

        $form = $this->createForm(ListingType::class, $listing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('file')->getData()) {
                $listingFileUploadService->addBannerFileFromUpload($listing, $form->get('file')->getData());
            }
            $customFieldsForListingFormService->saveCustomFieldsToListing($listing, $request->request->get('form_custom_field'));

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_listing_index', [
                'id' => $listing->getId(),
            ]);
        }

        return $this->render('listing/edit.html.twig', [
            'listing' => $listing,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/listing/{id}", name="listing_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Listing $listing, CurrentUserService $currentUserService): Response
    {
        if ($currentUserService->getUser() !== $listing->getUser()) {
            throw new UnauthorizedHttpException('user of listing do not match current user');
        }

        if ($this->isCsrfTokenValid('delete'.$listing->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($listing);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_listing_index');
    }
}
