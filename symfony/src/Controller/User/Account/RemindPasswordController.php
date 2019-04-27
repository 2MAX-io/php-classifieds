<?php

declare(strict_types=1);

namespace App\Controller\User\Account;

use App\Entity\Token;
use App\Entity\TokenField;
use App\Entity\User;
use App\Form\User\RemindPasswordType;
use App\Service\FlashBag\FlashService;
use App\Service\System\Token\TokenService;
use App\Service\User\Account\RemindPasswordService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RemindPasswordController extends AbstractController
{
    /**
     * @Route("/remind-password", name="app_remind_password")
     */
    public function remindPassword(
        Request $request,
        RemindPasswordService $remindPasswordService,
        FlashService $flashService
    ): Response {
        $form = $this->createForm(RemindPasswordType::class, []);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $remindPasswordService->sendRemindConfirmation($form->get(RemindPasswordType::EMAIL_FIELD)->getData());

            $this->getDoctrine()->getManager()->flush();

            $flashService->addFlash(
                FlashService::SUCCESS_ABOVE_FORM,
                'trans.To remind password, please click confirmation link that you would receive on your email address'
            );

            return $this->redirectToRoute('app_remind_password');
        }

        return $this->render('user/account/remind_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/remind-password/confirm/{token}", name="app_remind_password_confirm")
     */
    public function remindPasswordConfirm(
        string $token,
        RemindPasswordService $remindPasswordService,
        TokenService $tokenService,
        FlashService $flashService
    ): Response {
        $tokenEntity = $tokenService->getToken($token, Token::USER_PASSWORD_REMIND);

        if ($tokenEntity === null) {
            $flashService->addFlash(
                FlashService::ERROR_ABOVE_FORM,
                'trans.Confirmation link is invalid or expired, check if confirmation link is correct'
            );

            return $this->redirectToRoute('app_remind_password');
        }

        $userId = $tokenEntity->getFieldByName(TokenField::USER_ID_FIELD);
        $newHashedPassword = $tokenEntity->getFieldByName(TokenField::REMINDED_HASHED_PASSWORD);
        if (!$userId || !$newHashedPassword) {
            $flashService->addFlash(
                FlashService::ERROR_ABOVE_FORM,
                'trans.Confirmation link is invalid or expired, check if confirmation link is correct'
            );

            return $this->redirectToRoute('app_remind_password');
        }
        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

        if (!$user instanceof User) {
            $flashService->addFlash(
                FlashService::ERROR_ABOVE_FORM,
                'trans.Confirmation link is invalid or expired, check if confirmation link is correct'
            );

            return $this->redirectToRoute('app_remind_password');
        }

        $remindPasswordService->setHashedPassword(
            $user,
            $newHashedPassword
        );

        $this->getDoctrine()->getManager()->flush();

        $flashService->addFlash(
            FlashService::SUCCESS_ABOVE_FORM,
            'trans.Password reset has been successful'
        );

        return $this->redirectToRoute('app_login');
    }
}
