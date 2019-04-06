<?php

declare(strict_types=1);

namespace App\Controller\Admin\Category;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\CustomField;
use App\Enum\ParamEnum;
use App\Enum\SortConfig;
use App\Form\Admin\CustomFieldType;
use App\Helper\ExceptionHelper;
use App\Helper\JsonHelper;
use App\Repository\CategoryRepository;
use App\Repository\CustomFieldRepository;
use App\Service\Admin\CustomField\CategorySelection\CustomFieldCategoriesService;
use App\Service\Admin\CustomField\CustomFieldCategoriesOrderService;
use App\Service\Admin\CustomField\CustomFieldService;
use App\Service\System\FlashBag\FlashService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class CustomFieldController extends AbstractAdminController
{
    public const CSRF_CUSTOM_FIELDS_SAVE_ORDER = 'csrf_adminCustomFieldsSaveOrder';

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(CategoryRepository $categoryRepository, EntityManagerInterface $em)
    {
        $this->categoryRepository = $categoryRepository;
        $this->em = $em;
    }

    /**
     * @Route("/admin/red5/custom-field/list", name="app_admin_custom_field_list", methods={"GET"})
     */
    public function customFieldList(
        CustomFieldRepository $customFieldRepository,
        CsrfTokenManagerInterface $csrfTokenManager
    ): Response {
        $this->denyUnlessAdmin();

        return $this->render('admin/custom_field/index.html.twig', [
            'customFieldList' => $customFieldRepository->findBy([], ['sort' => Criteria::ASC]),
            ParamEnum::DATA_FOR_JS => [
                ParamEnum::CSRF_TOKEN => $csrfTokenManager->getToken(self::CSRF_CUSTOM_FIELDS_SAVE_ORDER)->getValue(),
            ],
        ]);
    }

    /**
     * @Route("/admin/red5/custom-field/new", name="app_admin_custom_field_new", methods={"GET","POST"})
     */
    public function new(
        Request $request,
        CustomFieldCategoriesService $customFieldCategoriesService,
        CustomFieldCategoriesOrderService $customFieldCategoriesOrderService,
        CustomFieldService $customFieldService
    ): Response {
        $this->denyUnlessAdmin();

        if ($request->get('categoryId', false)) {
            $category = $this->categoryRepository->find($request->get('categoryId'));
        }

        $customField = new CustomField();
        $customField->setSearchable(true);
        $customField->setSort(SortConfig::LAST_VALUE);
        $form = $this->createForm(CustomFieldType::class, $customField);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $customFieldCategoriesService->saveCategoriesForCustomField(
                $customField,
                $request->get('selectedCategories', [])
            );
            $this->em->persist($customField);
            $this->em->flush();

            $customFieldCategoriesOrderService->reorder();
            $customFieldService->reorder();

            return $this->redirectToRoute('app_admin_custom_field_edit', [
                'id' => $customField->getId(),
            ]);
        }

        return $this->render('admin/custom_field/new.html.twig', [
            'categoriesForCustomFieldList' => $customFieldCategoriesService->getCategoriesForCustomFieldList(
                $customField,
                $customFieldCategoriesService->getSelectedCategoriesFromRequest($request),
                $category ?? null
            ),
            'customField' => $customField,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/red5/custom-field/{id}/edit", name="app_admin_custom_field_edit", methods={"GET","POST"})
     */
    public function edit(
        Request $request,
        CustomField $customField,
        CustomFieldCategoriesService $customFieldCategoriesService,
        CustomFieldCategoriesOrderService $customFieldForCategoryOrderService,
        CsrfTokenManagerInterface $csrfTokenManager
    ): Response {
        $this->denyUnlessAdmin();

        $form = $this->createForm(CustomFieldType::class, $customField);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $customFieldCategoriesService->saveCategoriesForCustomField(
                $customField,
                $request->get('selectedCategories', [])
            );
            $this->em->flush();

            $customFieldForCategoryOrderService->reorder();

            return $this->redirectToRoute('app_admin_custom_field_edit', [
                'id' => $customField->getId(),
            ]);
        }

        return $this->render('admin/custom_field/edit.html.twig', [
            'categoriesForCustomFieldList' => $customFieldCategoriesService->getCategoriesForCustomFieldList(
                $customField,
                $customFieldCategoriesService->getSelectedCategoriesFromRequest($request),
            ),
            'customField' => $customField,
            'form' => $form->createView(),
            ParamEnum::DATA_FOR_JS => [
                ParamEnum::CSRF_TOKEN => $csrfTokenManager->getToken(CustomFieldOptionController::CSRF_SAVE_ORDER)->getValue(),
            ],
        ]);
    }

    /**
     * @Route("/admin/red5/custom-field/{id}/delete", name="app_admin_custom_field_delete", methods={"DELETE"})
     */
    public function delete(
        Request $request,
        CustomField $customField,
        CustomFieldService $customFieldService,
        FlashService $flashService,
        LoggerInterface $logger
    ): Response {
        $this->denyUnlessAdmin();

        if (!$this->isCsrfTokenValid('csrf_deleteCustomField'.$customField->getId(), $request->request->get('_token'))) {
            throw new InvalidCsrfTokenException('token not valid');
        }

        try {
            $this->em->beginTransaction();
            $customFieldService->deleteValueFromListings($customField);
            $this->em->remove($customField);
            $this->em->flush();
            $this->em->commit();
        } /* @noinspection PhpRedundantCatchClauseInspection */ catch (ForeignKeyConstraintViolationException $e) {
            $logger->notice('db constraint error during deletion', ExceptionHelper::flatten($e, [$e->getMessage()]));
            $this->em->rollback();
            $flashService->addFlash(
                FlashService::ERROR_ABOVE_FORM,
                'trans.To delete custom field, you must first delete all dependencies like: custom fields assigned to categories, custom field options'
            );

            return $this->redirectToRoute('app_admin_custom_field_edit', ['id' => $customField->getId()]);
        }

        return $this->redirectToRoute('app_admin_custom_field_list');
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

        if (!$this->isCsrfTokenValid(static::CSRF_CUSTOM_FIELDS_SAVE_ORDER, $request->headers->get(ParamEnum::CSRF_HEADER))) {
            throw new InvalidCsrfTokenException('token not valid');
        }

        $requestContentArray = JsonHelper::toArray($request->getContent());
        $customFieldService->saveOrder($requestContentArray['orderedIdList']);
        $this->em->flush();

        $customFieldService->reorder();

        return $this->json([]);
    }
}
