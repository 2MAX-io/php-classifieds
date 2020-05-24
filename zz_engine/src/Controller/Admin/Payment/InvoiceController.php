<?php

declare(strict_types=1);

namespace App\Controller\Admin\Payment;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\Invoice;
use App\Service\Invoice\InvoiceService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvoiceController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/invoice/invoice-list", name="app_admin_invoice_list", methods={"GET"})
     */
    public function adminInvoiceList(Request $request, InvoiceService $invoiceService): Response
    {
        $this->denyUnlessAdmin();

        $paginationDto = $invoiceService->getInvoiceList((int) $request->get('page', 1));

        /** @var Invoice[] $payments */
        $payments = $paginationDto->getResults();

        return $this->render('admin/invoice/index.html.twig', [
            'invoices' => $payments,
            'pager' => $paginationDto->getPager(),
        ]);
    }

    /**
     * @Route("/admin/red5/invoice/{id}", name="app_admin_invoice_show", methods={"GET"})
     */
    public function invoiceShow(Invoice $invoice): Response
    {
        $this->denyUnlessAdmin();

        return $this->render('admin/invoice/show.html.twig', [
            'invoice' => $invoice,
        ]);
    }
}
