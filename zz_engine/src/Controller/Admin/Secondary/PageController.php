<?php

declare(strict_types=1);

namespace App\Controller\Admin\Secondary;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\Page;
use App\Form\Admin\PageType;
use App\Repository\PageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/page", name="app_admin_page_index", methods={"GET"})
     */
    public function index(PageRepository $pageRepository): Response
    {
        $this->denyUnlessAdmin();

        return $this->render('admin/page/index.html.twig', [
            'pages' => $pageRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/red5/page/new", name="app_admin_page_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $this->denyUnlessAdmin();

        $page = new Page();
        $page->setEnabled(true);
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($page);
            $em->flush();

            return $this->redirectToRoute('app_admin_page_edit', [
                'id' => $page->getId(),
            ]);
        }

        return $this->render('admin/page/new.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/red5/page/{id}/edit", name="app_admin_page_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Page $page): Response
    {
        $this->denyUnlessAdmin();

        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_admin_page_edit', [
                'id' => $page->getId(),
            ]);
        }

        return $this->render('admin/page/edit.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/red5/page/{id}", name="app_admin_page_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Page $page): Response
    {
        $this->denyUnlessAdmin();

        if ($this->isCsrfTokenValid('delete'.$page->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($page);
            $em->flush();
        }

        return $this->redirectToRoute('app_admin_page_index');
    }
}
