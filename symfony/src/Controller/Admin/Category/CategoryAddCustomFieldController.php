<?php

declare(strict_types=1);

namespace App\Controller\Admin\Category;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\Category;
use App\Entity\CustomFieldJoinCategory;
use App\Form\Admin\CategoryAddCustomFieldType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryAddCustomFieldController extends AbstractAdminController
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
        Category $category
    ): Response {
        $this->denyUnlessAdmin();

        $customFieldJoinCategory = new CustomFieldJoinCategory();
        $customFieldJoinCategory->setCategory($category);
        $customFieldJoinCategory->setSort(999999999);
        $form = $this->createForm(CategoryAddCustomFieldType::class, $customFieldJoinCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($customFieldJoinCategory);
            $em->flush();

            return $this->redirectToRoute('app_admin_custom_field_edit', [
                'id' => $customFieldJoinCategory->getCustomField()->getId(),
            ]);
        }

        return $this->render('admin/category/add_custom_field.html.twig', [
            'customFieldJoinCategory' => $customFieldJoinCategory,
            'form' => $form->createView(),
        ]);
    }
}
