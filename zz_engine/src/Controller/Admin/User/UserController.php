<?php

declare(strict_types=1);

namespace App\Controller\Admin\User;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\User;
use App\Form\Admin\AdminUserEditType;
use App\Helper\StringHelper;
use App\Service\Admin\User\UserListService;
use App\Service\User\Account\Secondary\EncodePasswordService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractAdminController
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
     * @Route("/admin/red5/user", name="app_admin_user_list", methods={"GET"})
     */
    public function userListForAdmin(
        Request $request,
        UserListService $userListService
    ): Response {
        $this->denyUnlessAdmin();

        $paginationDto = $userListService->getUserList(
            (int) $request->get('page', 1),
            $request->get('query'),
        );

        return $this->render('admin/user/index.html.twig', [
            'users' => $paginationDto->getResults(),
            'pager' => $paginationDto->getPager(),
        ]);
    }

    /**
     * @Route("/admin/red5/user/{id}/edit", name="app_admin_user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, EncodePasswordService $encodePasswordService): Response
    {
        $this->denyUnlessAdmin();
        $originalUser = clone $user;

        $form = $this->createForm(AdminUserEditType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!StringHelper::emptyTrim($user->getPlainPassword())) {
                $encodedPassword = $encodePasswordService->getEncodedPassword($user, $user->getPlainPassword());
                $user->setPassword($encodedPassword);
            }
            $this->em->flush();

            return $this->redirectToRoute('app_admin_user_edit', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $originalUser,
            'form' => $form->createView(),
        ]);
    }
}
