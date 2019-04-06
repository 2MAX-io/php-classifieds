<?php

declare(strict_types=1);

namespace App\Controller\Admin\Secondary;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\Page;
use App\Form\Admin\PageType;
use App\Repository\PageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

class PageController extends AbstractAdminController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/admin/red5/page", name="app_admin_page_list", methods={"GET"})
     */
    public function pageListForAdmin(PageRepository $pageRepository): Response
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
            $this->em->persist($page);
            $this->em->flush();

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
        $originalPage = clone $page;

        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->redirectToRoute('app_admin_page_edit', [
                'id' => $page->getId(),
            ]);
        }

        return $this->render('admin/page/edit.html.twig', [
            'page' => $originalPage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/red5/page/{id}/delete", name="app_admin_page_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Page $page): Response
    {
        $this->denyUnlessAdmin();

        if (!$this->isCsrfTokenValid('csrf_deletePage'.$page->getId(), $request->request->get('_token'))) {
            throw new InvalidCsrfTokenException('token not valid');
        }
        $this->em->remove($page);
        $this->em->flush();

        return $this->redirectToRoute('app_admin_page_list');
    }
}
