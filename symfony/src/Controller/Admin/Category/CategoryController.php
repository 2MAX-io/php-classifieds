<?php

declare(strict_types=1);

namespace App\Controller\Admin\Category;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\Category;
use App\Form\Admin\AdminCategoryType;
use App\Service\Admin\Category\AdminCategoryService;
use App\Service\Category\TreeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/category", name="app_admin_category")
     */
    public function index(AdminCategoryService $categoryService): Response
    {
        $this->denyUnlessAdmin();

        return $this->render('admin/category/index.html.twig', [
            'categoryList' => $categoryService->getCategoryList(),
        ]);
    }

    /**
     * @Route("/admin/red5/category/rebuild", name="app_admin_category_rebuild")
     */
    public function rebuild(TreeService $treeService): Response
    {
        $this->denyUnlessAdmin();

        $treeService->rebuild();

        return new Response('done');
    }

    /**
     * @Route("/admin/red5/category/new", name="app_admin_category_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $this->denyUnlessAdmin();

        $category = new Category();
        $form = $this->createForm(AdminCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('category_index');
        }

        return $this->render('admin/category/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/red5/category/{id}/edit", name="app_admin_category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Category $category): Response
    {
        $this->denyUnlessAdmin();

        $form = $this->createForm(AdminCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('category_index', [
                'id' => $category->getId(),
            ]);
        }

        return $this->render('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/red5/category/{id}", name="app_admin_category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Category $category): Response
    {
        $this->denyUnlessAdmin();

        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('category_index');
    }
}
