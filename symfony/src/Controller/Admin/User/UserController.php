<?php

declare(strict_types=1);

namespace App\Controller\Admin\User;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\User;
use App\Form\Admin\UserType;
use App\Service\Admin\User\UserListService;
use App\Service\System\Pagination\PaginationService;
use App\Service\User\Account\EncodePasswordService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/user", name="admin_user_index", methods={"GET"})
     */
    public function index(
        Request $request,
        UserListService $userListService,
        PaginationService $paginationService
    ): Response {
        $this->denyUnlessAdmin();

        $paginationDto = $userListService->getUserList((int) $request->get('page', 1));

        return $this->render('admin/user/index.html.twig', [
            'users' => $paginationDto->getResults(),
            'pagination' => $paginationService->getPaginationHtml($paginationDto->getPager()),
            'pager' => $paginationDto->getPager(),
        ]);
    }

    /**
     * @Route("/admin/red5/user/{id}/edit", name="admin_user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, EncodePasswordService $encodePasswordService): Response
    {
        $this->denyUnlessAdmin();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty(trim($user->getPlainPassword()))) {
                $user->setPassword($encodePasswordService->getEncodedPassword($user, $user->getPlainPassword()));
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_user_edit', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
