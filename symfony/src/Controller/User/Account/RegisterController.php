<?php

declare(strict_types=1);

namespace App\Controller\User\Account;

use App\Entity\Token;
use App\Entity\TokenField;
use App\Form\User\RegisterType;
use App\Repository\UserRepository;
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
    public function register(
        Request $request,
        CreateUserService $createUserService,
        FlashService $flashService
    ): Response {
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
        TokenService $tokenService,
        UserRepository $userRepository
    ): Response {
        $tokenEntity = $tokenService->getToken($token, Token::USER_REGISTER_TYPE);

        if ($tokenEntity === null) {
            $flashService->addFlash(
                FlashService::ERROR_ABOVE_FORM,
                'trans.Confirmation link is invalid or expired'
            );

            return $this->redirectToRoute('app_register');
        }

        $userEmail = $tokenEntity->getFieldByName(TokenField::USER_EMAIL_FIELD);
        if ($userEmail === null) {
            $flashService->addFlash(
                FlashService::ERROR_ABOVE_FORM,
                'trans.Confirmation link is invalid or expired'
            );

            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->findByEmail($userEmail);
        if ($user->getEmail() !== $userEmail) {
            $flashService->addFlash(
                FlashService::ERROR_ABOVE_FORM,
                'trans.Confirmation link is invalid or expired'
            );

            return $this->redirectToRoute('app_register');
        }

        $registerConfirmService->confirmRegistration($user);

        $this->getDoctrine()->getManager()->flush();

        if ($user->getEnabled()) {
            $flashService->addFlash(
                FlashService::SUCCESS_ABOVE_FORM,
                'trans.You have been successfully registered. Now you can add some listings.'
            );
        }

        return $this->redirectToRoute('app_listing_new');
    }
}
