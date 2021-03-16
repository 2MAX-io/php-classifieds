<?php

declare(strict_types=1);

namespace App\Controller\User\Payment\Invoice;

use App\Controller\User\Base\AbstractUserController;
use App\Form\UserInvoiceDetailsType;
use App\Security\CurrentUserService;
use App\Service\System\FlashBag\FlashService;
use App\Service\User\Invoice\UserInvoiceDetailsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserInvoiceDetailsController extends AbstractUserController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    public function __construct(CurrentUserService $currentUserService, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->currentUserService = $currentUserService;
    }

    /**
     * @Route("/user/invoice/user-invoice-details", name="app_user_invoice_details")
     */
    public function userInvoiceDetails(
        Request $request,
        UserInvoiceDetailsService $userInvoiceDetailsService,
        FlashService $flashService
    ): Response {
        $this->dennyUnlessUser();

        $userInvoiceDetails = $userInvoiceDetailsService->getOrCreateUserInvoiceDetails();
        if (!$userInvoiceDetails->getEmailToSendInvoice()) {
            $userInvoiceDetails->setEmailToSendInvoice($this->currentUserService->getUser()->getEmail());
        }
        $form = $this->createForm(UserInvoiceDetailsType::class, $userInvoiceDetails);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($userInvoiceDetails);
            $this->em->flush();

            $flashService->addFlash(
                FlashService::SUCCESS_ABOVE_FORM,
                'trans.Saved successfully',
            );

            return $this->redirectToRoute('app_user_invoice_details');
        }

        return $this->render('user/invoice/user_invoice_details_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
