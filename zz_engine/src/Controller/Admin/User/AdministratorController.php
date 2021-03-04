<?php

declare(strict_types=1);

namespace App\Controller\Admin\User;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\System\Admin;
use App\Form\Admin\AdministratorEditType;
use App\Form\Admin\AdministratorNewType;
use App\Helper\StringHelper;
use App\Service\Admin\User\AdministratorListService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdministratorController extends AbstractAdminController
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
     * @Route("/admin/red5/administrator/list", name="app_admin_administrator_list", methods={"GET"})
     */
    public function administratorListForAdmin(
        Request $request,
        AdministratorListService $administratorListService
    ): Response {
        $this->denyUnlessAdmin();

        $paginationDto = $administratorListService->getAdminList(
            (int) $request->get('page', 1),
            $request->get('query'),
        );

        return $this->render('admin/administrator/index.html.twig', [
            'users' => $paginationDto->getResults(),
            'pager' => $paginationDto->getPager(),
        ]);
    }

    /**
     * @Route("/admin/red5/administrator/new", name="app_admin_administrator_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $userPasswordEncoder): Response
    {
        $this->denyUnlessAdmin();

        $admin = new Admin();
        $admin->setEnabled(true);
        $admin->setRoles([Admin::ROLE_ADMIN]);
        $form = $this->createForm(AdministratorNewType::class, $admin);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!StringHelper::emptyTrim($admin->getPlainPassword())) {
                $encodedPassword = $userPasswordEncoder->encodePassword($admin, $admin->getPlainPassword());
                $admin->setPassword($encodedPassword);
            }

            $this->em->persist($admin);
            $this->em->flush();

            return $this->redirectToRoute('app_admin_administrator_edit', [
                'id' => $admin->getId(),
            ]);
        }

        return $this->render('admin/administrator/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/red5/administrator/{id}/edit", name="app_admin_administrator_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Admin $admin, UserPasswordEncoderInterface $userPasswordEncoder): Response
    {
        $this->denyUnlessAdmin();
        $originalAdmin = clone $admin;

        $form = $this->createForm(AdministratorEditType::class, $admin);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!StringHelper::emptyTrim($admin->getPlainPassword())) {
                $encodedPassword = $userPasswordEncoder->encodePassword($admin, $admin->getPlainPassword());
                $admin->setPassword($encodedPassword);
            }

            $this->em->flush();

            return $this->redirectToRoute('app_admin_administrator_edit', [
                'id' => $admin->getId(),
            ]);
        }

        return $this->render('admin/administrator/edit.html.twig', [
            'user' => $originalAdmin,
            'form' => $form->createView(),
        ]);
    }
}
