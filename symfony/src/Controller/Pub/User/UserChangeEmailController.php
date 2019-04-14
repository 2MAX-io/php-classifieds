<?php

declare(strict_types=1);

namespace App\Controller\Pub\User;

use App\Form\User\ChangeEmailType;
use App\Security\CurrentUserService;
use App\Service\FlashBag\FlashInterface;
use App\Service\FlashBag\FlashService;
use App\Service\User\Create\ChangeEmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserChangeEmailController extends AbstractController
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
            $changeEmailService->changeEmail(
                $currentUserService->getUser(),
                $form->get(ChangeEmailType::FORM_NEW_EMAIL)->getData()
            );

            $this->getDoctrine()->getManager()->flush();

            $flashService->addFlash(
                FlashInterface::SUCCESS_ABOVE_FORM,
                'trans.Email password has been successfully changed'
            );

            return $this->redirectToRoute('app_user_change_email');
        }

        return $this->render('user/change_email.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
