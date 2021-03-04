<?php

declare(strict_types=1);

namespace App\Controller\User\Setting;

use App\Controller\User\Base\AbstractUserController;
use App\Form\User\Setting\Dto\UserSettingsDto;
use App\Form\User\Setting\UserSettingsType;
use App\Security\CurrentUserService;
use App\Service\User\Settings\UserSettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserSettingsController extends AbstractUserController
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
     * @Route("/user/account/settings/user-settings", name="app_user_settings")
     */
    public function userSettings(
        Request $request,
        UserSettingsService $userSettingsService,
        CurrentUserService $currentUserService
    ): Response {
        $this->dennyUnlessUser();

        $user = $currentUserService->getUser();
        $userSettingsDto = UserSettingsDto::fromUser($user);
        $form = $this->createForm(UserSettingsType::class, $userSettingsDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userSettingsService->save($userSettingsDto, $user);
            $this->em->flush();

            return $this->redirectToRoute('app_user_settings');
        }

        return $this->render('user/settings/user_settings.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
