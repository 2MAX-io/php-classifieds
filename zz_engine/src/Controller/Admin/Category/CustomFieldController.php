<?php

declare(strict_types=1);

namespace App\Controller\Admin\Category;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\Category;
use App\Entity\CustomField;
use App\Exception\UserVisibleMessageException;
use App\Form\Admin\CustomFieldType;
use App\Helper\ExceptionHelper;
use App\Helper\Json;
use App\Repository\CustomFieldRepository;
use App\Service\Admin\CustomField\CategorySelection\CustomFieldCategorySelectionService;
use App\Service\Admin\CustomField\CustomFieldForCategoryService;
use App\Service\Admin\CustomField\CustomFieldService;
use App\Service\System\Sort\SortService;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomFieldController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/custom-field/", name="app_admin_custom_field_index", methods={"GET"})
     */
    public function index(CustomFieldRepository $customFieldRepository): Response
    {
        $this->denyUnlessAdmin();

        return $this->render('admin/custom_field/index.html.twig', [
            'custom_fields' => $customFieldRepository->findBy([], ['sort' => 'ASC']),
        ]);
    }

    /**
     * @Route("/admin/red5/custom-field/new", name="app_admin_custom_field_new", methods={"GET","POST"})
     */
    public function new(
        Request $request,
        CustomFieldCategorySelectionService $customFieldCategorySelectionService,
        CustomFieldForCategoryService $customFieldForCategoryService
    ): Response {
        $this->denyUnlessAdmin();

        $em = $this->getDoctrine()->getManager();
        if ($request->get('categoryId', false)) {
            $category = $em->getRepository(Category::class)->find($request->get('categoryId'));
        }

        $customField = new CustomField();
        $customField->setSearchable(true);
        $customField->setSort(SortService::LAST_VALUE);
        $form = $this->createForm(CustomFieldType::class, $customField);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $customFieldCategorySelectionService->saveSelection(
                $customField,
                $request->get('selectedCategories', [])
            );
            $em->persist($customField);
            $em->flush();

            $customFieldForCategoryService->reorder();

            return $this->redirectToRoute('app_admin_custom_field_edit', [
                'id' => $customField->getId(),
            ]);
        }

        return $this->render('admin/custom_field/new.html.twig', [
            'categorySelectionList' => $customFieldCategorySelectionService->getCategorySelectionList(
                $customField,
                $category ?? null
            ),
            'custom_field' => $customField,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/red5/custom-field/{id}/edit", name="app_admin_custom_field_edit", methods={"GET","POST"})
     */
    public function edit(
        Request $request,
        CustomField $customField,
        CustomFieldCategorySelectionService $customFieldCategorySelectionService,
        CustomFieldForCategoryService $customFieldForCategoryService
    ): Response {
        $this->denyUnlessAdmin();

        $form = $this->createForm(CustomFieldType::class, $customField);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customFieldCategorySelectionService->saveSelection(
                $customField,
                $request->get('selectedCategories', [])
            );
            $this->getDoctrine()->getManager()->flush();

            $customFieldForCategoryService->reorder();

            return $this->redirectToRoute('app_admin_custom_field_edit', [
                'id' => $customField->getId(),
            ]);
        }

        return $this->render('admin/custom_field/edit.html.twig', [
            'categorySelectionList' => $customFieldCategorySelectionService->getCategorySelectionList($customField),
            'custom_field' => $customField,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/red5/custom-field/{id}", name="app_admin_custom_field_delete", methods={"DELETE"})
     */
    public function delete(Request $request, CustomField $customField, LoggerInterface $logger): Response
    {
        $this->denyUnlessAdmin();

        if ($this->isCsrfTokenValid('delete'.$customField->getId(), $request->request->get('_token'))) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($customField);
                $em->flush();
            } /** @noinspection PhpRedundantCatchClauseInspection */ catch (ForeignKeyConstraintViolationException $e) {
                $logger->notice('constraint error during deletion', ExceptionHelper::flatten($e, [$e->getMessage()]));
                throw new UserVisibleMessageException(
                    'trans.To delete custom field, you must first delete all dependencies like: custom fields assigned to categories, custom field options',
                    [],
                    0,
                    $e
                );
            }
        }

        return $this->redirectToRoute('app_admin_custom_field_index');
    }

    /**
     * @Route(
     *     "/admin/red5/custom-field/save-order",
     *     name="app_admin_custom_field_save_order",
     *     methods={"POST"},
     *     options={"expose": true},
     * )
     */
    public function saveOrder(
        Request $request,
        CustomFieldService $customFieldService
    ): Response {
        $this->denyUnlessAdmin();

        if ($this->isCsrfTokenValid('adminCustomFieldsSaveSort', $request->headers->get('x-csrf-token'))) {
            $em = $this->getDoctrine()->getManager();

            $requestContentArray  = Json::decodeToArray($request->getContent());
            $customFieldService->saveOrder($requestContentArray['orderedIdList']);
            $em->flush();

            $customFieldService->reorder();
        }

        return $this->json([]);
    }
}
