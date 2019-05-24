<?php

declare(strict_types=1);

namespace App\Controller\Admin\Category;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\Category;
use App\Entity\CustomFieldJoinCategory;
use App\Form\Admin\CategoryAddCustomFieldType;
use App\Helper\Json;
use App\Service\Admin\CustomField\CustomFieldForCategoryService;
use App\Service\System\Sort\SortService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryCustomFieldController extends AbstractAdminController
{
    /**
     * @Route(
     *     "/admin/red5/category/{id}/add-custom-field",
     *     name="app_admin_category_add_custom_field",
     *     methods={"GET","POST"}
     * )
     */
    public function addCustomField(
        Request $request,
        Category $category,
        CustomFieldForCategoryService $customFieldForCategoryService
    ): Response {
        $this->denyUnlessAdmin();

        $customFieldJoinCategory = new CustomFieldJoinCategory();
        $customFieldJoinCategory->setCategory($category);
        $customFieldJoinCategory->setSort(SortService::LAST_VALUE);
        $form = $this->createForm(CategoryAddCustomFieldType::class, $customFieldJoinCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($customFieldJoinCategory);
            $em->flush();

            $customFieldForCategoryService->reorder();

            return $this->redirectToRoute('app_admin_category_edit', [
                'id' => $customFieldJoinCategory->getCategory()->getId(),
            ]);
        }

        return $this->render('admin/category/add_custom_field.html.twig', [
            'customFieldJoinCategory' => $customFieldJoinCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(
     *     "/admin/red5/category/custom-fields/save-order",
     *     name="app_admin_category_custom_fields_save_order",
     *     methods={"POST"},
     *     options={"expose": true},
     * )
     */
    public function saveCustomFieldsOrderInCategory(
        Request $request,
        CustomFieldForCategoryService $customFieldForCategoryService
    ): Response {
        $this->denyUnlessAdmin();

        if ($this->isCsrfTokenValid('adminCustomFieldsInCategorySaveSort', $request->headers->get('x-csrf-token'))) {
            $em = $this->getDoctrine()->getManager();

            $requestContentArray  = Json::decodeToArray($request->getContent());
            $customFieldForCategoryService->saveOrder($requestContentArray['orderedIdList']);
            $em->flush();

            $customFieldForCategoryService->reorder();
        }

        return $this->json([]);
    }

    /**
     * @Route(
     *     "/admin/red5/category/custom-field-join-category/{id}",
     *     name="app_admin_category_custom_field_join_category_delete",
     *     methods={"DELETE"}
     * )
     */
    public function deleteCustomFieldJoinCategory(
        Request $request,
        CustomFieldJoinCategory $customFieldJoinCategory,
        CustomFieldForCategoryService $customFieldForCategoryService
    ): Response {
        $this->denyUnlessAdmin();

        if ($this->isCsrfTokenValid('deleteCustomFieldFromCategory'.$customFieldJoinCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($customFieldJoinCategory);
            $entityManager->flush();

            $customFieldForCategoryService->reorder();
        }

        return $this->redirectToRoute('app_admin_category_edit', [
            'id' => $customFieldJoinCategory->getCategory()->getId(),
        ]);
    }
}
