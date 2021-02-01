<?php

declare(strict_types=1);

namespace App\Controller\Admin\Category;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\Category;
use App\Enum\ParamEnum;
use App\Form\Admin\AdminCategorySaveType;
use App\Helper\ExceptionHelper;
use App\Helper\Json;
use App\Service\Admin\Category\AdminCategoryService;
use App\Service\Admin\Category\CategoryPictureUploadService;
use App\Service\Category\TreeService;
use App\Service\FlashBag\FlashService;
use App\Service\System\Sort\SortService;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class AdminCategoryController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/category", name="app_admin_category")
     */
    public function index(AdminCategoryService $categoryService, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $this->denyUnlessAdmin();

        return $this->render('admin/category/index.html.twig', [
            'categoryList' => $categoryService->getCategoryList(),
            ParamEnum::DATA_FOR_JS => [
                'adminCategorySaveSort' => $csrfTokenManager->getToken('adminCategorySaveSort')->getValue(),
            ],
        ]);
    }

    /**
     * @Route("/admin/red5/category/new", name="app_admin_category_new", methods={"GET","POST"})
     */
    public function new(
        Request $request,
        TreeService $treeService,
        CategoryPictureUploadService $categoryPictureUploadService
    ): Response {
        $this->denyUnlessAdmin();

        $em = $this->getDoctrine()->getManager();
        $parentCategory = $em->getRepository(Category::class)->find((int) $request->get('parentCategory'));

        $category = new Category();
        $category->setLvl(0);
        $category->setLft(0);
        $category->setRgt(0);
        $category->setSort(SortService::LAST_VALUE);
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
            $em->persist($category);
            $em->flush();

            $treeService->rebuild();
            $em->flush();

            if ($saveAndAddButton->getClickedButton() instanceof SubmitButton && $saveAndAddButton->getClickedButton()->isClicked()) {
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
        TreeService $treeService,
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
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $treeService->rebuild();
            $em->flush();

            if ($saveAndAddButton->getClickedButton() instanceof SubmitButton && $saveAndAddButton->getClickedButton()->isClicked()) {
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
                'adminCustomFieldsInCategorySaveSort' => $csrfTokenManager->getToken('adminCustomFieldsInCategorySaveSort')->getValue(),
            ],
        ]);
    }

    /**
     * @Route("/admin/red5/category/{id}", name="app_admin_category_delete", methods={"DELETE"})
     */
    public function delete(
        Request $request,
        Category $category,
        TreeService $treeService,
        FlashService $flashService,
        LoggerInterface $logger
    ): Response {
        $this->denyUnlessAdmin();

        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($category);
                $em->flush();

                $treeService->rebuild();
                $em->flush();
            } /** @noinspection PhpRedundantCatchClauseInspection */ catch (ForeignKeyConstraintViolationException $e) {
                $logger->notice('constraint error during deletion', ExceptionHelper::flatten($e, [$e->getMessage()]));
                $flashService->addFlash(
                    FlashService::ERROR_ABOVE_FORM,
                    'trans.To delete category, you must first delete or move all dependencies like: listings in this category, subcategories, assigned custom fields, featured packages'
                );

                return $this->redirectToRoute('app_admin_category_edit', ['id' => $category->getId()]);
            }
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
        AdminCategoryService $adminCategoryService,
        TreeService $treeService
    ): Response {
        $this->denyUnlessAdmin();

        if ($this->isCsrfTokenValid('adminCategorySaveSort', $request->headers->get('x-csrf-token'))) {
            $em = $this->getDoctrine()->getManager();

            $requestContentArray  = Json::toArray($request->getContent());
            $adminCategoryService->saveOrder($requestContentArray['orderedIdList']);
            $em->flush();

            $treeService->rebuild();
            $em->flush();
        }

        return $this->json([]);
    }
}
