<?php

declare(strict_types=1);

namespace App\Controller\Admin\Category;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\CustomField;
use App\Entity\CustomFieldOption;
use App\Form\Admin\CustomFieldOptionType;
use App\Helper\Json;
use App\Service\Admin\CustomField\CustomFieldOptionService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomFieldOptionController extends AbstractAdminController
{
    /**
     * @Route(
     *     "/admin/red5/custom-field/{id}/add-option",
     *     name="app_admin_custom_field_add_option",
     *     methods={"GET","POST"}
     * )
     */
    public function addCustomFieldOption(
        Request $request,
        CustomField $customField,
        CustomFieldOptionService $customFieldOptionService
    ): Response {
        $this->denyUnlessAdmin();

        $customFieldOption = new CustomFieldOption();
        $customFieldOption->setCustomField($customField);
        $customFieldOption->setSort(999999999);
        $form = $this->createForm(CustomFieldOptionType::class, $customFieldOption);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($customFieldOption);
            $em->flush();

            $customFieldOptionService->reorder();

            return $this->redirectToRoute('app_admin_custom_field_edit_option', [
                'id' => $customFieldOption->getId(),
            ]);
        }

        return $this->render('admin/custom_field/option/custom_field_option.html.twig', [
            'customFieldOption' => $customFieldOption,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(
     *     "/admin/red5/custom-field/option/{id}/edit-option",
     *     name="app_admin_custom_field_edit_option",
     *     methods={"GET","POST"}
     * )
     */
    public function editCustomFieldOption(
        Request $request,
        CustomFieldOption $customFieldOption,
        CustomFieldOptionService $customFieldOptionService
    ): Response {
        $this->denyUnlessAdmin();

        $form = $this->createForm(CustomFieldOptionType::class, $customFieldOption);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($customFieldOption);
            $em->flush();

            $customFieldOptionService->reorder();

            return $this->redirectToRoute('app_admin_custom_field_edit_option', [
                'id' => $customFieldOption->getId(),
            ]);
        }

        return $this->render('admin/custom_field/option/custom_field_option.html.twig', [
            'customFieldOption' => $customFieldOption,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(
     *     "/admin/red5/custom-field/option/{id}/delete-option",
     *     name="app_admin_custom_field_option_delete",
     *     methods={"DELETE"}
     * )
     */
    public function delete(
        Request $request,
        CustomFieldOption $customFieldOption,
        CustomFieldOptionService $customFieldOptionService
    ): Response {
        $this->denyUnlessAdmin();

        if ($this->isCsrfTokenValid('delete'.$customFieldOption->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($customFieldOption);
            $entityManager->flush();

            $customFieldOptionService->reorder();
        }

        return $this->redirectToRoute('app_admin_custom_field_edit', ['id' => $customFieldOption->getCustomField()->getId()]);
    }

    /**
     * @Route(
     *     "/admin/red5/custom-field/options/save-order-of-options",
     *     name="app_admin_custom_field_options_save_order",
     *     methods={"POST"},
     *     options={"expose": true},
     * )
     */
    public function saveCustomFieldOptionsOrder(
        Request $request,
        CustomFieldOptionService $customFieldOptionService
    ): Response {
        $this->denyUnlessAdmin();

        if ($this->isCsrfTokenValid('adminCustomFieldOptionsSaveSort', $request->headers->get('x-csrf-token'))) {
            $em = $this->getDoctrine()->getManager();

            $requestContentArray  = Json::decodeToArray($request->getContent());
            $customFieldOptionService->saveOrderOfOptions($requestContentArray['orderedIdList']);
            $em->flush();

            $customFieldOptionService->reorder();
        }

        return $this->json([]);
    }
}
