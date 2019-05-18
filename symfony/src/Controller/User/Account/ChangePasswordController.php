<?php

declare(strict_types=1);

namespace App\Controller\User\Account;

use App\Entity\Token;
use App\Entity\TokenField;
use App\Form\User\Account\ChangePasswordType;
use App\Security\CurrentUserService;
use App\Service\FlashBag\FlashService;
use App\Service\System\Token\TokenService;
use App\Service\User\Account\ChangePasswordService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChangePasswordController extends AbstractController
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
            $changePasswordService->sendConfirmation(
                $currentUserService->getUser(),
                $form->get(ChangePasswordType::FORM_NEW_PASSWORD)->getData()
            );

            $this->getDoctrine()->getManager()->flush();

            $flashService->addFlash(
                FlashService::SUCCESS_ABOVE_FORM,
                'trans.To finalize password change, open your email account and click confirmation link'
            );

            return $this->redirectToRoute('app_user_change_password');
        }

        return $this->render('user/account/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/confirm/user-password-change/{token}", name="app_user_change_password_confirm")
     */
    public function changePasswordConfirm(
        string $token,
        ChangePasswordService $changePasswordService,
        TokenService $tokenService,
        FlashService $flashService
    ): Response {
        $tokenEntity = $tokenService->getToken($token, Token::USER_PASSWORD_CHANGE_TYPE);
        if ($tokenEntity === null) {
            $flashService->addFlash(
                FlashService::ERROR_ABOVE_FORM,
                'trans.Confirmation link is invalid or expired, check if confirmation link is correct'
            );

            return $this->redirectToRoute('app_user_change_password');
        }

        $newHashedPassword = $tokenEntity->getFieldByName(TokenField::CHANGED_NEW_HASHED_PASSWORD);
        $userId = $tokenEntity->getFieldByName(TokenField::USER_ID_FIELD);
        if (!$newHashedPassword || !$userId) {
            $flashService->addFlash(
                FlashService::ERROR_ABOVE_FORM,
                'trans.Confirmation link is invalid or expired, check if confirmation link is correct'
            );

            return $this->redirectToRoute('app_user_change_password');
        }

        $changePasswordService->setHashedPassword(
            $tokenService->getUserFromToken($tokenEntity),
            $newHashedPassword
        );
        $tokenService->markTokenUsed($tokenEntity);

        $this->getDoctrine()->getManager()->flush();

        $flashService->addFlash(
            FlashService::SUCCESS_ABOVE_FORM,
            'trans.Password change has been successful'
        );

        return $this->redirectToRoute('app_user_change_password');
    }
}
