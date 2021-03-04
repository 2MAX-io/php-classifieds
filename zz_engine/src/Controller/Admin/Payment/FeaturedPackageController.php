<?php

declare(strict_types=1);

namespace App\Controller\Admin\Payment;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\FeaturedPackage;
use App\Form\Admin\FeaturedPackageType;
use App\Repository\FeaturedPackageRepository;
use App\Service\Admin\FeaturedPackage\CategorySelection\FeaturedPackageCategoriesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

class FeaturedPackageController extends AbstractAdminController
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
     * @Route("/admin/red5/featured-package", name="app_admin_featured_package_list", methods={"GET"})
     */
    public function featuredPackageList(FeaturedPackageRepository $featuredPackageRepository): Response
    {
        $this->denyUnlessAdmin();

        return $this->render('admin/featured_package/index.html.twig', [
            'featuredPackages' => $featuredPackageRepository->findBy(['removed' => false]),
        ]);
    }

    /**
     * @Route("/admin/red5/featured-package/new", name="app_admin_featured_package_new", methods={"GET","POST"})
     */
    public function new(
        Request $request,
        FeaturedPackageCategoriesService $categoriesService
    ): Response {
        $this->denyUnlessAdmin();

        $featuredPackage = new FeaturedPackage();
        $form = $this->createForm(FeaturedPackageType::class, $featuredPackage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($featuredPackage);

            $categoriesService->saveSelectedCategories(
                $featuredPackage,
                $request->get('selectedCategories', [])
            );
            $this->em->flush();

            return $this->redirectToRoute('app_admin_featured_package_edit', [
                'id' => $featuredPackage->getId(),
            ]);
        }

        return $this->render('admin/featured_package/new.html.twig', [
            'featuredPackage' => $featuredPackage,
            'categorySelectionList' => $categoriesService->getCategorySelectionList(
                $featuredPackage,
                $categoriesService->getSelectedCategoriesIdFromRequest($request),
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
        FeaturedPackageCategoriesService $categoriesService
    ): Response {
        $this->denyUnlessAdmin();

        $form = $this->createForm(FeaturedPackageType::class, $featuredPackage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $categoriesService->saveSelectedCategories(
                $featuredPackage,
                $request->get('selectedCategories', [])
            );
            $this->em->flush();

            return $this->redirectToRoute('app_admin_featured_package_edit', [
                'id' => $featuredPackage->getId(),
            ]);
        }

        return $this->render('admin/featured_package/edit.html.twig', [
            'featuredPackage' => $featuredPackage,
            'categorySelectionList' => $categoriesService->getCategorySelectionList(
                $featuredPackage,
                $categoriesService->getSelectedCategoriesIdFromRequest($request),
            ),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/red5/featured-package/{id}/delete", name="app_admin_featured_package_delete", methods={"DELETE"})
     */
    public function delete(Request $request, FeaturedPackage $featuredPackage): Response
    {
        $this->denyUnlessAdmin();

        if (!$this->isCsrfTokenValid('csrf_deleteFeaturedPackage'.$featuredPackage->getId(), $request->request->get('_token'))) {
            throw new InvalidCsrfTokenException('token not valid');
        }
        $featuredPackage->setRemoved(true);
        $this->em->persist($featuredPackage);
        $this->em->flush();

        return $this->redirectToRoute('app_admin_featured_package_list');
    }
}
