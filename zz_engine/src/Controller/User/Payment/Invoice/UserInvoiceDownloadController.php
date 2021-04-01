<?php

declare(strict_types=1);

namespace App\Controller\User\Payment\Invoice;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\Invoice;
use App\Enum\EnvironmentEnum;
use App\Helper\SlugHelper;
use App\Security\CurrentUserService;
use App\Service\User\Invoice\UserInvoiceSingleService;
use Symfony\Component\HttpFoundation\Response;
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
    ): Response {
        $this->dennyUnlessUser();
        $this->dennyUnlessUserAllowedInvoice($invoice);

        $domPdf = $userInvoiceSingleService->renderInvoicePdf($invoice);
        $invoiceFileName = SlugHelper::getSlug(
            $translator->trans('trans.invoice_file_prefix').$invoice->getInvoiceNumber(),
            '_',
        );
        if (EnvironmentEnum::TEST === ($_ENV['APP_ENV'] ?? '')) {
            return new Response($domPdf->output(), Response::HTTP_OK);
        }
        $domPdf->stream(
            $invoiceFileName,
            [
                'Attachment' => true,
            ],
        );

        return new Response('response should be streamed', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    private function dennyUnlessUserAllowedInvoice(Invoice $invoice): void
    {
        if ($invoice->getUser() !== $this->currentUserService->getUser()) {
            throw new UnauthorizedHttpException('Invoice does not belong to current user');
        }
    }
}
