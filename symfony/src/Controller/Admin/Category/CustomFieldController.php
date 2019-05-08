<?php

declare(strict_types=1);

namespace App\Controller\Admin\Category;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\CustomField;
use App\Form\Admin\CustomFieldType;
use App\Helper\Json;
use App\Repository\CustomFieldRepository;
use App\Service\Admin\CustomField\CategorySelection\CustomFieldCategorySelectionService;
use App\Service\Admin\CustomField\CustomFieldService;
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
            'custom_fields' => $customFieldRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/red5/custom-field/new", name="app_admin_custom_field_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $this->denyUnlessAdmin();

        $customField = new CustomField();
        $form = $this->createForm(CustomFieldType::class, $customField);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($customField);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_custom_field_edit', [
                'id' => $customField->getId(),
            ]);
        }

        return $this->render('admin/custom_field/new.html.twig', [
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
        CustomFieldCategorySelectionService $customFieldCategorySelectionService
    ): Response {
        $this->denyUnlessAdmin();

        $form = $this->createForm(CustomFieldType::class, $customField);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

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
    public function delete(Request $request, CustomField $customField): Response
    {
        $this->denyUnlessAdmin();

        if ($this->isCsrfTokenValid('delete'.$customField->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($customField);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_custom_field_index');
    }

    /**
     * @Route(
     *     "/admin/red5/custom-field/options/save-order-of-options",
     *     name="app_admin_custom_field_options_save_order",
     *     methods={"POST"},
     *     options={"expose": true},
     * )
     */
    public function saveCustomFieldOptionsOrder(Request $request, CustomFieldService $customFieldService): Response
    {
        $this->denyUnlessAdmin();

        if ($this->isCsrfTokenValid('adminCustomFieldOptionsSaveSort', $request->headers->get('x-csrf-token'))) {
            $em = $this->getDoctrine()->getManager();

            $requestContentArray  = Json::decodeToArray($request->getContent());
            $customFieldService->saveOrderOfOptions($requestContentArray['orderedIdList']);
            $em->flush();
        }

        return $this->json([]);
    }
}
