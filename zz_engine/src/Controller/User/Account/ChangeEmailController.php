<?php

declare(strict_types=1);

namespace App\Controller\User\Account;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\Token;
use App\Entity\TokenField;
use App\Form\User\Account\ChangeEmailType;
use App\Security\CurrentUserService;
use App\Service\FlashBag\FlashService;
use App\Service\System\Token\TokenService;
use App\Service\User\Account\ChangeEmailService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChangeEmailController extends AbstractUserController
{
    /**
     * @Route("/user/account/changeEmail", name="app_user_change_email")
     */
    public function changeEmail(
        Request $request,
        CurrentUserService $currentUserService,
        ChangeEmailService $changeEmailService,
        FlashService $flashService
    ): Response {
        $this->dennyUnlessUser();

        $form = $this->createForm(ChangeEmailType::class, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $changeEmailService->sendConfirmation(
                $currentUserService->getUserOrNull(),
                $form->get(ChangeEmailType::FORM_NEW_EMAIL)->getData()
            );

            $this->getDoctrine()->getManager()->flush();

            $flashService->addFlash(
                FlashService::SUCCESS_ABOVE_FORM,
                'trans.To finalize email change, open your email account and click confirmation link'
            );

            return $this->redirectToRoute('app_user_change_email');
        }

        return $this->render('user/account/change_email.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(
     *     "/confirm/user-email-change/previous/{token}",
     *     name="app_user_change_email_previous_email_confirmation"
     * )
     */
    public function changeEmailPreviousConfirmation(
        string $token,
        ChangeEmailService $changeEmailService,
        TokenService $tokenService,
        FlashService $flashService
    ): Response {
        $tokenEntity = $tokenService->getToken($token, Token::USER_EMAIL_CHANGE_TYPE);
        if ($tokenEntity === null) {
            $flashService->addFlash(
                FlashService::ERROR_ABOVE_FORM,
                'trans.Confirmation link is invalid or expired, check if confirmation link is correct'
            );

            return $this->redirectToRoute('app_user_change_email');
        }
        if ($tokenEntity->getUsed()) {
            $flashService->addFlash(
                FlashService::ERROR_ABOVE_FORM,
                'trans.Action has been confirmed before, and requested action has been completed'
            );

            return $this->redirectToRoute('app_user_change_email');
        }

        $newEmail = $tokenEntity->getFieldByName(TokenField::USER_NEW_EMAIL_FIELD);
        $userId = $tokenEntity->getFieldByName(TokenField::USER_ID_FIELD);
        if (!$newEmail || !$userId) {
            $flashService->addFlash(
                FlashService::ERROR_ABOVE_FORM,
                'trans.Confirmation link is invalid or expired, check if confirmation link is correct'
            );

            return $this->redirectToRoute('app_user_change_email');
        }

        $changeEmailService->changeEmail(
            $tokenService->getUserFromToken($tokenEntity),
            $newEmail
        );
        $tokenService->markTokenUsed($tokenEntity);

        $this->getDoctrine()->getManager()->flush();

        $flashService->addFlash(
            FlashService::SUCCESS_ABOVE_FORM,
            'trans.Email address change has been successful'
        );

        return $this->redirectToRoute('app_user_change_email');
    }
}
