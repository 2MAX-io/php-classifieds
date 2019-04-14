<?php

declare(strict_types=1);

namespace App\Controller\Pub\User;

use App\Form\User\ChangePasswordType;
use App\Security\CurrentUserService;
use App\Service\FlashBag\FlashService;
use App\Service\User\Create\ChangePasswordService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserChangePasswordController extends AbstractController
{
    /**
     * @Route("/user/account/changePassword", name="app_user_change_password")
     */
    public function changePassword(
        Request $request,
        ChangePasswordService $changePasswordService,
        CurrentUserService $currentUserService,
        FlashService $flashService
    ): Response {
        $form = $this->createForm(ChangePasswordType::class, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $changePasswordService->changePassword(
                $currentUserService->getUser(),
                $form->get(ChangePasswordType::FORM_NEW_PASSWORD)->getData()
            );

            $this->getDoctrine()->getManager()->flush();

            $flashService->addFlash(
                FlashService::SUCCESS_ABOVE_FORM,
                'trans.Password has been successfully changed'
            );

            return $this->redirectToRoute('app_user_change_password');
        }

        return $this->render('user/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
