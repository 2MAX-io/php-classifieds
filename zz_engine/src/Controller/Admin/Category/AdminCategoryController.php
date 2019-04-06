<?php

declare(strict_types=1);

namespace App\Controller\Admin\Category;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\Category;
use App\Enum\ParamEnum;
use App\Enum\SortConfig;
use App\Form\Admin\AdminCategorySaveType;
use App\Helper\ExceptionHelper;
use App\Helper\JsonHelper;
use App\Service\Admin\Category\AdminCategoryService;
use App\Service\Admin\Category\CategoryPictureUploadService;
use App\Service\System\FlashBag\FlashService;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class AdminCategoryController extends AbstractAdminController
{
    public const CSRF_CATEGORY_SORT_SAVE = 'csrf_adminCategorySortSave';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/admin/red5/category", name="app_admin_category")
     */
    public function adminCategory(
        AdminCategoryService $categoryService,
        CsrfTokenManagerInterface $csrfTokenManager
    ): Response {
        $this->denyUnlessAdmin();

        return $this->render('admin/category/index.html.twig', [
            'categoryList' => $categoryService->getCategoriesWithChildren(),
            ParamEnum::DATA_FOR_JS => [
                ParamEnum::CSRF_TOKEN => $csrfTokenManager->getToken(static::CSRF_CATEGORY_SORT_SAVE)->getValue(),
            ],
        ]);
    }

    /**
     * @Route("/admin/red5/category/new", name="app_admin_category_new", methods={"GET","POST"})
     */
    public function new(
        Request $request,
        AdminCategoryService $adminCategoryService,
        CategoryPictureUploadService $categoryPictureUploadService
    ): Response {
        $this->denyUnlessAdmin();

        $category = new Category();
        $category->setLvl(0);
        $category->setLft(0);
        $category->setRgt(0);
        $category->setSort(SortConfig::LAST_VALUE);
        $parentCategory = $this->em->getRepository(Category::class)->find((int) $request->get('parentCategory'));
        if ($parentCategory) {
            $category->setParent($parentCategory);
        }

        /** @var Form $form */
        $form = $this->createForm(AdminCategorySaveType::class, $category);
        $saveAndAddButton = $form->add(AdminCategorySaveType::SAVE_AND_ADD, SubmitType::class, [
            'label' => 'trans.Save and Add',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('picture')->getData()) {
                $categoryPictureUploadService->savePicture($category, $form->get('picture')->getData());
            }
            $this->em->persist($category);
            $this->em->flush();

            $adminCategoryService->resetOrderOfAllCategories();
            $this->em->flush();

            $clickedButton = $saveAndAddButton->getClickedButton();
            if ($clickedButton instanceof SubmitButton && $clickedButton->isClicked()) {
                return $this->redirectToRoute('app_admin_category_new', [
                    'parentCategory' => $category->getParentNotNull()->getId(),
                ]);
            }

            return $this->redirectToRoute('app_admin_category_edit', [
                'id' => $category->getId(),
            ]);
        }

        return $this->render('admin/category/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/red5/category/{id}/edit", name="app_admin_category_edit", methods={"GET","POST"})
     */
    public function edit(
        Request $request,
        Category $category,
        AdminCategoryService $adminCategoryService,
        CategoryPictureUploadService $categoryPictureUploadService,
        CsrfTokenManagerInterface $csrfTokenManager
    ): Response {
        $this->denyUnlessAdmin();

        /** @var Form $form */
        $form = $this->createForm(AdminCategorySaveType::class, $category);
        $saveAndAddButton = $form->add(AdminCategorySaveType::SAVE_AND_ADD, SubmitType::class, [
            'label' => 'trans.Save and Add',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('picture')->getData()) {
                $categoryPictureUploadService->savePicture($category, $form->get('picture')->getData());
            }
            $this->em->flush();

            $adminCategoryService->resetOrderOfAllCategories();
            $this->em->flush();

            $clickedButton = $saveAndAddButton->getClickedButton();
            if ($clickedButton instanceof SubmitButton && $clickedButton->isClicked()) {
                return $this->redirectToRoute('app_admin_category_new', [
                    'parentCategory' => $category->getParentNotNull()->getId(),
                ]);
            }

            return $this->redirectToRoute('app_admin_category_edit', [
                'id' => $category->getId(),
            ]);
        }

        return $this->render('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            ParamEnum::DATA_FOR_JS => [
                ParamEnum::CSRF_TOKEN => $csrfTokenManager->getToken(CategoryCustomFieldController::CSRF_CUSTOM_FIELDS_FOR_CATEGORY_ORDER_SAVE)->getValue(),
            ],
        ]);
    }

    /**
     * @Route("/admin/red5/category/{id}/delete", name="app_admin_category_delete", methods={"DELETE"})
     */
    public function delete(
        Request $request,
        Category $category,
        AdminCategoryService $adminCategoryService,
        FlashService $flashService,
        LoggerInterface $logger
    ): Response {
        $this->denyUnlessAdmin();

        if (!$this->isCsrfTokenValid('csrf_deleteCategory'.$category->getId(), $request->request->get('_token'))) {
            throw new InvalidCsrfTokenException('token not valid');
        }

        try {
            $this->em->remove($category);
            $this->em->flush();

            $adminCategoryService->resetOrderOfAllCategories();
            $this->em->flush();
        } /* @noinspection PhpRedundantCatchClauseInspection */ catch (ForeignKeyConstraintViolationException $e) {
            $logger->notice('db constraint error during deletion', ExceptionHelper::flatten($e, [$e->getMessage()]));
            $flashService->addFlash(
                FlashService::ERROR_ABOVE_FORM,
                'trans.To delete category, you must first delete or move all dependencies like: listings in this category, subcategories, assigned custom fields, packages'
            );

            return $this->redirectToRoute('app_admin_category_edit', ['id' => $category->getId()]);
        }

        return $this->redirectToRoute('app_admin_category');
    }

    /**
     * @Route(
     *     "/admin/red5/category/save-order",
     *     name="app_admin_category_save_order",
     *     methods={"POST"},
     *     options={"expose": true},
     * )
     */
    public function saveOrder(
        Request $request,
        AdminCategoryService $adminCategoryService
    ): Response {
        $this->denyUnlessAdmin();

        if (!$this->isCsrfTokenValid(static::CSRF_CATEGORY_SORT_SAVE, $request->headers->get(ParamEnum::CSRF_HEADER))) {
            throw new InvalidCsrfTokenException('token not valid');
        }
        $requestContentArray = JsonHelper::toArray($request->getContent());
        $adminCategoryService->saveOrder($requestContentArray['orderedIdList']);
        $this->em->flush();

        $adminCategoryService->resetOrderOfAllCategories();
        $this->em->flush();

        return $this->json([]);
    }
}
