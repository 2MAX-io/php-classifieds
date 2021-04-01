<?php

declare(strict_types=1);

namespace App\Controller\Admin\Category;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\CustomField;
use App\Entity\CustomFieldOption;
use App\Enum\ParamEnum;
use App\Enum\SortConfig;
use App\Form\Admin\CustomFieldOptionType;
use App\Helper\JsonHelper;
use App\Service\Admin\CustomField\CustomFieldOptionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

class CustomFieldOptionController extends AbstractAdminController
{
    public const CSRF_SAVE_ORDER = 'csrf_adminCustomFieldOptionsOrderSave';

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
     *     "/admin/red5/custom-field-option/custom-field/{id}/add-option",
     *     name="app_admin_custom_field_option_add",
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
        $customFieldOption->setSort(SortConfig::LAST_VALUE);
        /** @var Form $form */
        $form = $this->createForm(CustomFieldOptionType::class, $customFieldOption);
        $saveAndAddButton = $form->add(CustomFieldOptionType::SAVE_AND_ADD, SubmitType::class, [
            'label' => 'trans.Save and Add',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($customFieldOption);
            $this->em->flush();

            $customFieldOptionService->reorder();

            $clickedButton = $saveAndAddButton->getClickedButton();
            if ($clickedButton instanceof SubmitButton && $clickedButton->isClicked()) {
                return $this->redirectToRoute('app_admin_custom_field_option_add', [
                    'id' => $customField->getId(),
                ]);
            }

            return $this->redirectToRoute('app_admin_custom_field_option_edit', [
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
     *     "/admin/red5/custom-field-option/{id}/edit-option",
     *     name="app_admin_custom_field_option_edit",
     *     methods={"GET","POST"}
     * )
     */
    public function editCustomFieldOption(
        Request $request,
        CustomFieldOption $customFieldOption,
        CustomFieldOptionService $customFieldOptionService
    ): Response {
        $this->denyUnlessAdmin();

        $oldCustomField = clone $customFieldOption;
        $form = $this->createForm(CustomFieldOptionType::class, $customFieldOption);
        /** @var Form $saveAndAddButton */
        $saveAndAddButton = $form->add(CustomFieldOptionType::SAVE_AND_ADD, SubmitType::class, [
            'label' => 'trans.Save and Add',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($customFieldOption);
            $this->em->flush();

            $customFieldOptionService->reorder();
            $customFieldOptionService->updateCustomFieldOptionValueForListings(
                $customFieldOption,
                $oldCustomField->getValue(),
                $customFieldOption->getValue(),
            );

            $clickedButton = $saveAndAddButton->getClickedButton();
            if ($clickedButton instanceof SubmitButton && $clickedButton->isClicked()) {
                return $this->redirectToRoute('app_admin_custom_field_option_add', [
                    'id' => $customFieldOption->getCustomFieldNotNull()->getId(),
                ]);
            }

            return $this->redirectToRoute('app_admin_custom_field_option_edit', [
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
     *     "/admin/red5/custom-field-option/{id}/delete",
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

        if (!$this->isCsrfTokenValid('csrf_deleteFieldOption'.$customFieldOption->getId(), $request->request->get('_token'))) {
            throw new InvalidCsrfTokenException('token not valid');
        }

        try {
            $this->em->beginTransaction();
            $customFieldOptionService->deleteCustomFieldOptionValuesFromListings($customFieldOption);
            $this->em->remove($customFieldOption);
            $this->em->flush();
            $this->em->commit();
        } catch (\Throwable $e) {
            $this->em->rollback();

            throw $e;
        }

        $customFieldOptionService->reorder();

        return $this->redirectToRoute(
            'app_admin_custom_field_edit',
            ['id' => $customFieldOption->getCustomFieldNotNull()->getId()]
        );
    }

    /**
     * @Route(
     *     "/admin/red5/custom-field-option/save-options-order",
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

        if (!$this->isCsrfTokenValid(self::CSRF_SAVE_ORDER, $request->headers->get(ParamEnum::CSRF_HEADER))) {
            throw new InvalidCsrfTokenException('token not valid');
        }

        $requestContentArray = JsonHelper::toArray($request->getContent());
        $customFieldOptionService->saveOrderOfOptions($requestContentArray['orderedIdList']);
        $this->em->flush();

        $customFieldOptionService->reorder();

        return $this->json([]);
    }
}
