<?php

declare(strict_types=1);

namespace App\Controller\Admin\User;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\Admin;
use App\Form\Admin\AdministratorType;
use App\Helper\Str;
use App\Service\Admin\User\AdministratorListService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdministratorController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/administrator-user", name="app_admin_administrator_index", methods={"GET"})
     */
    public function index(
        Request $request,
        AdministratorListService $administratorListService
    ): Response {
        $this->denyUnlessAdmin();

        $paginationDto = $administratorListService->getAdminList((int) $request->get('page', 1));

        return $this->render('admin/administrator/index.html.twig', [
            'users' => $paginationDto->getResults(),
            'pager' => $paginationDto->getPager(),
        ]);
    }

    /**
     * @Route("/admin/red5/administrator-user/new", name="app_admin_administrator_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $userPasswordEncoder): Response
    {
        $this->denyUnlessAdmin();

        $admin = new Admin();
        $admin->setEnabled(true);
        $admin->setRoles([Admin::ROLE_ADMIN]);
        $form = $this->createForm(AdministratorType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!Str::emptyTrim($admin->getPlainPassword())) {
                $admin->setPassword($userPasswordEncoder->encodePassword($admin, $admin->getPlainPassword()));
            }

            $this->getDoctrine()->getManager()->persist($admin);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_admin_administrator_edit', [
                'id' => $admin->getId(),
            ]);
        }

        return $this->render('admin/administrator/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/red5/administrator-user/{id}/edit", name="app_admin_administrator_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Admin $admin, UserPasswordEncoderInterface $userPasswordEncoder): Response
    {
        $this->denyUnlessAdmin();

        $form = $this->createForm(AdministratorType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!Str::emptyTrim($admin->getPlainPassword())) {
                $admin->setPassword($userPasswordEncoder->encodePassword($admin, $admin->getPlainPassword()));
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_admin_administrator_edit', [
                'id' => $admin->getId(),
            ]);
        }

        return $this->render('admin/administrator/edit.html.twig', [
            'user' => $admin,
            'form' => $form->createView(),
        ]);
    }
}
