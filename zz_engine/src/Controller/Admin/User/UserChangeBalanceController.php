<?php

declare(strict_types=1);

namespace App\Controller\Admin\User;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\User;
use App\Form\Admin\UserChangeBalanceType;
use App\Helper\IntegerHelper;
use App\Service\Money\UserBalanceHistoryService;
use App\Service\Money\UserBalanceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserChangeBalanceController extends AbstractAdminController
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
     * @Route("/admin/red5/user/change-balance/{id}", name="app_admin_user_change_balance")
     */
    public function changeBalanceOfUser(
        Request $request,
        User $user,
        UserBalanceService $userBalanceService,
        UserBalanceHistoryService $userBalanceHistoryService,
        TranslatorInterface $trans
    ): Response {
        $this->denyUnlessAdmin();

        $form = $this->createForm(UserChangeBalanceType::class, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $previousBalance = $userBalanceService->getCurrentBalance($user);
            $userBalanceChange = $userBalanceService->forceSetBalance(
                IntegerHelper::toInteger($form->get(UserChangeBalanceType::NEW_BALANCE)->getData() * 100),
                $user,
            );

            if ($form->get(UserChangeBalanceType::CHANGE_REASON)->getData()) {
                $userBalanceChange->setDescription(
                    $trans->trans(
                        'trans.Balance change by administrator to: %newBalance% from previous: %previousBalance%, reason: %reason%',
                        [
                            '%newBalance%' => $userBalanceChange->getBalanceFinal() / 100,
                            '%previousBalance%' => $previousBalance / 100,
                            '%reason%' => $form->get(UserChangeBalanceType::CHANGE_REASON)->getData(),
                        ]
                    )
                );
            } else {
                $userBalanceChange->setDescription(
                    $trans->trans(
                        'trans.Balance change by administrator to: %newBalance% from previous: %previousBalance%',
                        [
                            '%newBalance%' => $userBalanceChange->getBalanceFinal() / 100,
                            '%previousBalance%' => $previousBalance / 100,
                        ]
                    )
                );
            }

            $this->em->flush();

            return $this->redirectToRoute('app_admin_user_change_balance', ['id' => $user->getId()]);
        }

        return $this->render('admin/user/user_balance_change.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'currentBalance' => $userBalanceService->getCurrentBalance($user),
            'userBalanceHistoryList' => $userBalanceHistoryService->getHistoryList($user),
        ]);
    }
}
