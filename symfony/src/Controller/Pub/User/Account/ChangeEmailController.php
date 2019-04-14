<?php

declare(strict_types=1);

namespace App\Controller\Pub\User\Account;

use App\Entity\Token;
use App\Form\User\ChangeEmailType;
use App\Security\CurrentUserService;
use App\Service\FlashBag\FlashService;
use App\Service\System\Token\TokenService;
use App\Service\User\Create\ChangeEmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChangeEmailController extends AbstractController
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
        $form = $this->createForm(ChangeEmailType::class, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $changeEmailService->sendConfirmation(
                $currentUserService->getUser(),
                $form->get(ChangeEmailType::FORM_NEW_EMAIL)->getData()
            );

            $this->getDoctrine()->getManager()->flush();

            $flashService->addFlash(
                FlashService::SUCCESS_ABOVE_FORM,
                'trans.We send email change confirmation to both new and previous email, please click confirmation link in email to finish email change.'
            );

            return $this->redirectToRoute('app_user_change_email');
        }

        return $this->render('user/account/change_email.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/account/changeEmail/confirmation/previous/{token}", name="app_user_change_email_previous_email_confirmation")
     */
    public function changeEmailPreviousConfirmation(
        string $token,
        CurrentUserService $currentUserService,
        ChangeEmailService $changeEmailService,
        TokenService $tokenService,
        FlashService $flashService
    ): Response {
        $tokenEntity = $tokenService->getToken($token, Token::USER_EMAIL_CHANGE_TYPE);

        if ($tokenEntity === null) {
            $flashService->addFlash(
                FlashService::ERROR_ABOVE_FORM,
                'trans.Confirmation link is invalid or expired'
            );

            return $this->redirectToRoute('app_user_change_email');
        }

        $newEmail = $tokenEntity->getValueMain();

        if ($tokenEntity->getToken() === $currentUserService->getUser()->getConfirmationToken()) {
            $changeEmailService->changeEmail(
                $currentUserService->getUser(),
                $tokenEntity->getValueMain()
            );

            $this->getDoctrine()->getManager()->flush();

            $flashService->addFlash(
                FlashService::SUCCESS_ABOVE_FORM,
                'trans.Email change has been successful'
            );
        } else {
            if ($newEmail === $currentUserService->getUser()->getEmail()) {
                $flashService->addFlash(
                    FlashService::SUCCESS_ABOVE_FORM,
                    'trans.Email change has been successful'
                );
            } else {
                $flashService->addFlash(
                    FlashService::ERROR_ABOVE_FORM,
                    'trans.Email change failed, please check if confirmation link is correct'
                );
            }
        }

        return $this->redirectToRoute('app_user_change_email');
    }
}