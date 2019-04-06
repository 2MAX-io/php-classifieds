<?php

declare(strict_types=1);

namespace App\Controller\User\Account;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\System\Token;
use App\Entity\System\TokenField;
use App\Entity\User;
use App\Enum\ParamEnum;
use App\Form\User\Account\Register\RegisterType;
use App\Form\User\Account\Register\RegisterUserDto;
use App\Repository\UserRepository;
use App\Service\System\FlashBag\FlashService;
use App\Service\System\Token\TokenService;
use App\Service\User\Account\CreateUserService;
use App\Service\User\Account\RegisterConfirmService;
use App\Validator\Constraints\UserEmailNotTaken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\ConstraintViolation;

class RegisterController extends AbstractUserController
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
     * @Route("/register", name="app_register")
     */
    public function register(
        Request $request,
        CreateUserService $createUserService,
        RegisterConfirmService $registerConfirmService,
        FlashService $flashService
    ): Response {
        $registerDto = new RegisterUserDto();
        $form = $this->createForm(RegisterType::class, $registerDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $createUserService->registerUser($registerDto);
            $this->em->flush();

            $flashService->addFlash(
                FlashService::SUCCESS_ABOVE_FORM,
                'trans.To finish registration, click confirmation link that you will receive in your email'
            );

            return $this->redirectToRoute('app_login');
        }

        /** @var FormError $error */
        foreach ($form->get('email')->getErrors() as $error) {
            /** @var ConstraintViolation $cause */
            $cause = $error->getCause();
            if ($cause->getConstraint() instanceof UserEmailNotTaken) {
                $registerConfirmService->resendRegisterConfirm($registerDto);
                $this->em->flush();
            }
        }

        return $this->render('user/account/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/private/register/confirm/{token}", name="app_register_confirm")
     */
    public function confirmRegistration(
        Request $request,
        string $token,
        RegisterConfirmService $registerConfirmService,
        UserRepository $userRepository,
        FlashService $flashService,
        TokenService $tokenService
    ): Response {
        $tokenEntity = $tokenService->getToken($token, Token::USER_REGISTER_TYPE);
        if (null === $tokenEntity) {
            $flashService->addFlash(
                FlashService::ERROR_ABOVE_FORM,
                'trans.Confirmation link is invalid or expired, check if confirmation link is correct'
            );

            return $this->redirectToRoute('app_register');
        }
        if ($tokenEntity->getUsed()) {
            $flashService->addFlash(
                FlashService::ERROR_ABOVE_FORM,
                'trans.Action has been confirmed before, and requested action has been completed'
            );

            return $this->redirectToRoute('app_register');
        }

        $userEmail = $tokenEntity->getFieldByName(TokenField::USER_EMAIL_FIELD);
        if (null === $userEmail) {
            $flashService->addFlash(
                FlashService::ERROR_ABOVE_FORM,
                'trans.Confirmation link is invalid or expired, check if confirmation link is correct'
            );

            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->findByEmail($userEmail);
        if ($user instanceof User && $user->getEmail() !== $userEmail) {
            $flashService->addFlash(
                FlashService::ERROR_ABOVE_FORM,
                'trans.Confirmation link is invalid or expired, check if confirmation link is correct'
            );

            return $this->redirectToRoute('app_register');
        }

        $registerConfirmService->confirmRegistration($user);
        $tokenService->markTokenUsed($tokenEntity);
        $this->em->flush();

        if ($user->getEnabled()) {
            $flashService->addFlash(
                FlashService::SUCCESS_ABOVE_FORM,
                'trans.You have been successfully registered. Now you can add some listings.'
            );

            $request->getSession()->set(Security::LAST_USERNAME, $request->get(ParamEnum::USERNAME));
        }

        return $this->redirectToRoute('app_user_listing_new');
    }
}
