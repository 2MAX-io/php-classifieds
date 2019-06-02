<?php

declare(strict_types=1);

namespace App\Controller\Admin\Payment;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\FeaturedPackage;
use App\Form\Admin\FeaturedPackageType;
use App\Repository\FeaturedPackageRepository;
use App\Service\Admin\FeaturedPackage\CategorySelection\FeaturedPackageCategorySelectionService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeaturedPackageController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/featured-package", name="app_admin_featured_package_index", methods={"GET"})
     */
    public function index(FeaturedPackageRepository $featuredPackageRepository): Response
    {
        $this->denyUnlessAdmin();

        return $this->render('admin/featured_package/index.html.twig', [
            'featured_packages' => $featuredPackageRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/red5/featured-package/new", name="app_admin_featured_package_new", methods={"GET","POST"})
     */
    public function new(
        Request $request,
        FeaturedPackageCategorySelectionService $featuredPackageCategorySelectionService
    ): Response {
        $this->denyUnlessAdmin();

        $featuredPackage = new FeaturedPackage();
        $form = $this->createForm(FeaturedPackageType::class, $featuredPackage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($featuredPackage);
            $em->flush();

            return $this->redirectToRoute('app_admin_featured_package_edit', [
                'id' => $featuredPackage->getId(),
            ]);
        }

        return $this->render('admin/featured_package/new.html.twig', [
            'featured_package' => $featuredPackage,
            'categorySelectionList' => $featuredPackageCategorySelectionService->getCategorySelectionList(
                $featuredPackage,
                $category ?? null
            ),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/red5/featured-package/{id}/edit", name="app_admin_featured_package_edit", methods={"GET","POST"})
     */
    public function edit(
        Request $request,
        FeaturedPackage $featuredPackage,
        FeaturedPackageCategorySelectionService $featuredPackageCategorySelectionService
    ): Response {
        $this->denyUnlessAdmin();

        $form = $this->createForm(FeaturedPackageType::class, $featuredPackage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $featuredPackageCategorySelectionService->saveSelection(
                $featuredPackage,
                $request->get('selectedCategories', [])
            );
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_admin_featured_package_edit', [
                'id' => $featuredPackage->getId(),
            ]);
        }

        return $this->render('admin/featured_package/edit.html.twig', [
            'featured_package' => $featuredPackage,
            'categorySelectionList' => $featuredPackageCategorySelectionService->getCategorySelectionList(
                $featuredPackage,
                $category ?? null
            ),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/red5/featured-package/{id}", name="app_admin_featured_package_delete", methods={"DELETE"})
     */
    public function delete(Request $request, FeaturedPackage $featuredPackage): Response
    {
        $this->denyUnlessAdmin();

        if ($this->isCsrfTokenValid('delete'.$featuredPackage->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($featuredPackage);
            $em->flush();
        }

        return $this->redirectToRoute('app_admin_featured_package_edit', [
            'id' => $featuredPackage->getId(),
        ]);
    }
}
