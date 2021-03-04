<?php

declare(strict_types=1);

namespace App\Controller\User\Invoice;

use App\Controller\User\Base\AbstractUserController;
use App\Service\User\Invoice\UserInvoiceListService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserInvoiceListController extends AbstractUserController
{
    /**
     * @Route("/user/invoice/invoice-list", name="app_user_invoice_list")
     */
    public function userInvoiceList(UserInvoiceListService $invoiceListService): Response
    {
        $this->dennyUnlessUser();

        return $this->render('user/invoice/user_invoice_list.html.twig', [
            'invoiceList' => $invoiceListService->getInvoiceListForCurrentUser(),
        ]);
    }
}
