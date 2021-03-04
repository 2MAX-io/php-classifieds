<?php

declare(strict_types=1);

namespace App\Controller\User\Invoice;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\Invoice;
use App\Helper\SlugHelper;
use App\Security\CurrentUserService;
use App\Service\User\Invoice\UserInvoiceSingleService;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserInvoiceDownloadController extends AbstractUserController
{
    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    public function __construct(CurrentUserService $currentUserService)
    {
        $this->currentUserService = $currentUserService;
    }

    /**
     * @Route("/user/invoice/download/{invoice}", name="app_user_invoice_download")
     */
    public function userInvoiceDownload(
        Invoice $invoice,
        UserInvoiceSingleService $userInvoiceSingleService,
        TranslatorInterface $translator
    ): void {
        $this->dennyUnlessUser();
        $this->dennyUnlessUserAllowedInvoice($invoice);

        $domPdf = $userInvoiceSingleService->renderInvoicePdf($invoice);
        $invoiceFileName = SlugHelper::getSlug(
            $translator->trans('trans.invoice_file_prefix').$invoice->getInvoiceNumber(),
            '_',
        );
        $domPdf->stream(
            $invoiceFileName,
            [
                'Attachment' => true,
            ],
        );
    }

    private function dennyUnlessUserAllowedInvoice(Invoice $invoice): void
    {
        if ($invoice->getUser() !== $this->currentUserService->getUser()) {
            throw new UnauthorizedHttpException('Invoice does not belong to current user');
        }
    }
}
