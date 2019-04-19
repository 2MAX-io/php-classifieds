<?php

declare(strict_types=1);

namespace App\Controller\Pub\User\Account;

use App\Entity\Token;
use App\Form\User\RegisterType;
use App\Service\FlashBag\FlashService;
use App\Service\System\Token\TokenService;
use App\Service\User\Account\RegisterConfirmService;
use App\Service\User\Account\CreateUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, CreateUserService $createUserService, FlashService $flashService): Response
    {
        $form = $this->createForm(RegisterType::class, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $createUserService->registerUser($form->get(RegisterType::EMAIL_FIELD)->getData());

            $this->getDoctrine()->getManager()->flush();

            $flashService->addFlash(
                FlashService::SUCCESS_ABOVE_FORM,
                'trans.To finish registration, click confirmation link that you will receive in your email'
            );

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/account/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/register/confirm/{token}", name="app_register_confirm")
     */
    public function registerConfirm(
        string $token,
        RegisterConfirmService $registerConfirmService,
        FlashService $flashService,
        TokenService $tokenService
    ): Response {
        $tokenEntity = $tokenService->getToken($token, Token::USER_REGISTER_TYPE);

        if ($tokenEntity === null) {
            $flashService->addFlash(
                FlashService::ERROR_ABOVE_FORM,
                'trans.Confirmation link is invalid or expired'
            );

            return $this->redirectToRoute('app_register');
        }

        $userEmailFromToken = $tokenEntity->getValueMain();
        $user = $registerConfirmService->getUserByToken($token);

        if ($user === null || $user->getEmail() !== $userEmailFromToken) {
            $flashService->addFlash(
                FlashService::ERROR_ABOVE_FORM,
                'trans.Confirmation link is invalid or expired'
            );

            return $this->redirectToRoute('app_register');
        }

        if ($tokenEntity->getTokenString() === $user->getConfirmationToken()) {
            $registerConfirmService->confirmRegistration($user);

            $this->getDoctrine()->getManager()->flush();

            $flashService->addFlash(
                FlashService::SUCCESS_ABOVE_FORM,
                'trans.You have been successfully registered. Now you can add some listings.'
            );
        } else {
            if ($user->getEnabled()) {
                $flashService->addFlash(
                    FlashService::SUCCESS_ABOVE_FORM,
                    'trans.You have been successfully registered. Now you can add some listings.'
                );
            } else {
                $flashService->addFlash(
                    FlashService::ERROR_ABOVE_FORM,
                    'trans.Registration confirmation failed, please check if confirmation link is correct'
                );
            }
        }

        return $this->redirectToRoute('app_listing_new');
    }
}
