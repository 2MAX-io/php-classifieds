<?php

declare(strict_types=1);

namespace App\Controller\Admin\Category;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\CustomField;
use App\Form\Admin\CustomFieldOptionCopyDto;
use App\Form\Admin\CustomFieldOptionCopyType;
use App\Service\Admin\CustomField\CustomFieldOptionCopyService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomFieldOptionCopyController extends AbstractAdminController
{
    /**
     * @Route(
     *     "/admin/red5/custom-field/{id}/copy-options-from-other",
     *     name="app_admin_custom_field_option_copy",
     *     methods={"GET","POST"}
     * )
     */
    public function copyCustomFieldOptions(
        Request $request,
        CustomField $customField,
        CustomFieldOptionCopyService $customFieldOptionCopyService
    ): Response {
        $this->denyUnlessAdmin();

        $customFieldOptionCopyDto = new CustomFieldOptionCopyDto();
        $form = $this->createForm(CustomFieldOptionCopyType::class, $customFieldOptionCopyDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $customFieldOptionCopyService->copy($customFieldOptionCopyDto, $customField);
            $em->flush();

            return $this->redirectToRoute('app_admin_custom_field_edit', [
                'id' => $customField->getId(),
            ]);
        }

        return $this->render('admin/custom_field/option/custom_field_option_copy.html.twig', [
            'customField' => $customField,
            'form' => $form->createView(),
        ]);
    }
}
