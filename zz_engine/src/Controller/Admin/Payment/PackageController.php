<?php

declare(strict_types=1);

namespace App\Controller\Admin\Payment;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\Package;
use App\Form\Admin\PackageType;
use App\Repository\PackageRepository;
use App\Service\Admin\Package\CategorySelection\PackageCategoriesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

class PackageController extends AbstractAdminController
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
     * @Route("/admin/red5/package", name="app_admin_package_list", methods={"GET"})
     */
    public function packageList(PackageRepository $packageRepository): Response
    {
        $this->denyUnlessAdmin();

        return $this->render('admin/package/index.html.twig', [
            'packages' => $packageRepository->findBy(['removed' => false]),
        ]);
    }

    /**
     * @Route("/admin/red5/package/new", name="app_admin_package_new", methods={"GET","POST"})
     */
    public function new(
        Request $request,
        PackageCategoriesService $categoriesService
    ): Response {
        $this->denyUnlessAdmin();

        $package = new Package();
        $form = $this->createForm(PackageType::class, $package);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($package);

            $categoriesService->saveSelectedCategories(
                $package,
                $request->get('selectedCategories', [])
            );
            $this->em->flush();

            return $this->redirectToRoute('app_admin_package_edit', [
                'id' => $package->getId(),
            ]);
        }

        return $this->render('admin/package/new.html.twig', [
            'package' => $package,
            'categorySelectionList' => $categoriesService->getCategorySelectionList(
                $package,
                $categoriesService->getSelectedCategoriesIdFromRequest($request),
            ),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/red5/package/{id}/edit", name="app_admin_package_edit", methods={"GET","POST"})
     */
    public function edit(
        Request $request,
        Package $package,
        PackageCategoriesService $categoriesService
    ): Response {
        $this->denyUnlessAdmin();

        $form = $this->createForm(PackageType::class, $package);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $categoriesService->saveSelectedCategories(
                $package,
                $request->get('selectedCategories', [])
            );
            $this->em->flush();

            return $this->redirectToRoute('app_admin_package_edit', [
                'id' => $package->getId(),
            ]);
        }

        return $this->render('admin/package/edit.html.twig', [
            'package' => $package,
            'categorySelectionList' => $categoriesService->getCategorySelectionList(
                $package,
                $categoriesService->getSelectedCategoriesIdFromRequest($request),
            ),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/red5/package/{id}/delete", name="app_admin_package_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Package $package): Response
    {
        $this->denyUnlessAdmin();

        if (!$this->isCsrfTokenValid('csrf_deletePackage'.$package->getId(), $request->request->get('_token'))) {
            throw new InvalidCsrfTokenException('token not valid');
        }
        $package->setRemoved(true);
        $this->em->persist($package);
        $this->em->flush();

        return $this->redirectToRoute('app_admin_package_list');
    }
}
