<?php

declare(strict_types=1);

namespace App\Controller\Admin\Category;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\Category;
use App\Entity\CustomFieldForCategory;
use App\Enum\ParamEnum;
use App\Enum\SortConfig;
use App\Form\Admin\CategoryAddCustomFieldType;
use App\Helper\JsonHelper;
use App\Service\Admin\CustomField\CustomFieldCategoriesOrderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

class CategoryCustomFieldController extends AbstractAdminController
{
    public const CSRF_CUSTOM_FIELDS_FOR_CATEGORY_ORDER_SAVE = 'csrf_adminCustomFieldsForCategoryOrderSave';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route(
     *     "/admin/red5/category/{id}/add-custom-field",
     *     name="app_admin_category_add_custom_field",
     *     methods={"GET","POST"}
     * )
     */
    public function addCustomFieldForCategory(
        Request $request,
        Category $category,
        CustomFieldCategoriesOrderService $customFieldForCategoryService
    ): Response {
        $this->denyUnlessAdmin();

        $customFieldForCategory = new CustomFieldForCategory();
        $customFieldForCategory->setCategory($category);
        $customFieldForCategory->setSort(SortConfig::LAST_VALUE);
        $form = $this->createForm(CategoryAddCustomFieldType::class, $customFieldForCategory);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($customFieldForCategory);
            $this->em->flush();

            $customFieldForCategoryService->reorder();

            return $this->redirectToRoute('app_admin_category_edit', [
                'id' => $customFieldForCategory->getCategoryNotNull()->getId(),
            ]);
        }

        return $this->render('admin/category/add_custom_field.html.twig', [
            'customFieldForCategory' => $customFieldForCategory,
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
    public function saveCustomFieldsForCategoryOrder(
        Request $request,
        CustomFieldCategoriesOrderService $customFieldForCategoryService
    ): Response {
        $this->denyUnlessAdmin();

        if (!$this->isCsrfTokenValid(
            self::CSRF_CUSTOM_FIELDS_FOR_CATEGORY_ORDER_SAVE,
            $request->headers->get(ParamEnum::CSRF_HEADER)
        )) {
            throw new InvalidCsrfTokenException('token not valid');
        }

        $requestContentArray = JsonHelper::toArray($request->getContent());
        $customFieldForCategoryService->saveOrder($requestContentArray['orderedIdList']);
        $this->em->flush();

        $customFieldForCategoryService->reorder();

        return $this->json([]);
    }

    /**
     * @Route(
     *     "/admin/red5/category/custom-field-for-category/{id}/delete",
     *     name="app_admin_category_custom_field_for_category_delete",
     *     methods={"DELETE"}
     * )
     */
    public function deleteCustomFieldForCategory(
        Request $request,
        CustomFieldForCategory $customFieldForCategory,
        CustomFieldCategoriesOrderService $customFieldForCategoryService
    ): Response {
        $this->denyUnlessAdmin();

        if (!$this->isCsrfTokenValid(
            'csrf_deleteCustomFieldForCategory'.$customFieldForCategory->getId(),
            $request->request->get('_token')
        )) {
            throw new InvalidCsrfTokenException('token not valid');
        }
        $this->em->remove($customFieldForCategory);
        $this->em->flush();
        $customFieldForCategoryService->reorder();

        return $this->redirectToRoute('app_admin_category_edit', [
            'id' => $customFieldForCategory->getCategoryNotNull()->getId(),
        ]);
    }
}
