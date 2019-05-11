<?php

declare(strict_types=1);

namespace App\Controller\Admin\User;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\User;
use App\Form\Admin\UserChangeBalanceType;
use App\Helper\Integer;
use App\Service\Money\UserBalanceService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserChangeBalanceController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/user/change-balance/{id}", name="app_admin_user_change_balance")
     */
    public function userChangeBalance(
        Request $request,
        User $user,
        UserBalanceService $userBalanceService,
        TranslatorInterface $trans
    ): Response {
        $this->denyUnlessAdmin();

        $form = $this->createForm(UserChangeBalanceType::class, []);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $previousBalance = $userBalanceService->getCurrentBalance($user);
            $userBalanceChange = $userBalanceService->forceSetBalance(
                Integer::toInteger($form->get(UserChangeBalanceType::NEW_BALANCE)->getData() * 100),
                $user
            );
            $userBalanceChange->setDescription(
                $trans->trans(
                    'trans.Balanced change by administrator to: %newBalance% from previous: %previousBalance%',
                    [
                        '%newBalance%' => $userBalanceChange->getBalanceFinal() / 100,
                        '%previousBalance%' => $previousBalance / 100
                    ]
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_user_change_balance', ['id' => $user->getId()]);
        }

        return $this->render('admin/user/user_balance_change.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'currentBalance' => $userBalanceService->getCurrentBalance($user),
        ]);
    }
}
