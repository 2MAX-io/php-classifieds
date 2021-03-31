<?php

declare(strict_types=1);

namespace App\Controller\User\Account;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\System\Token;
use App\Entity\System\TokenField;
use App\Entity\User;
use App\Enum\ParamEnum;
use App\Form\User\Account\RemindPasswordType;
use App\Service\System\FlashBag\FlashService;
use App\Service\System\Token\TokenService;
use App\Service\User\Account\RemindPasswordService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class RemindPasswordController extends AbstractUserController
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
            $remindPasswordService->sendRemindConfirmation(
                $form->get(RemindPasswordType::EMAIL_FIELD)->getData(),
            );
            $this->em->flush();

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
     * @Route("/private/remind-password/confirm/{token}", name="app_remind_password_confirm")
     */
    public function remindPasswordConfirm(
        Request $request,
        string $token,
        RemindPasswordService $remindPasswordService,
        TokenService $tokenService,
        FlashService $flashService
    ): Response {
        $tokenEntity = $tokenService->getToken($token, Token::USER_PASSWORD_REMIND);
        if (null === $tokenEntity) {
            $flashService->addFlash(
                FlashService::ERROR_ABOVE_FORM,
                'trans.Confirmation link is invalid or expired, check if confirmation link is correct'
            );

            return $this->redirectToRoute('app_remind_password');
        }
        if ($tokenEntity->getUsed()) {
            $flashService->addFlash(
                FlashService::ERROR_ABOVE_FORM,
                'trans.Action has been confirmed before, and requested action has been completed'
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

        $remindPasswordService->remindPasswordConfirmed(
            $user,
            $newHashedPassword,
        );
        $tokenService->markTokenUsed($tokenEntity);
        $this->em->flush();

        $flashService->addFlash(
            FlashService::SUCCESS_ABOVE_FORM,
            'trans.Password reset has been successful'
        );
        $request->getSession()->set(Security::LAST_USERNAME, $request->get(ParamEnum::USERNAME));

        return $this->redirectToRoute('app_login');
    }
}
