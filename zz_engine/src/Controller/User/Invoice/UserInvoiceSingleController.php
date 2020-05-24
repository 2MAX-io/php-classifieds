<?php

declare(strict_types=1);

namespace App\Controller\User\Invoice;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\Invoice;
use App\Helper\FileHelper;
use App\Helper\FilePath;
use App\Helper\SlugHelper;
use App\Security\CurrentUserService;
use App\Service\Setting\SettingsService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserInvoiceSingleController extends AbstractUserController
{
    /**
     * @Route("/user/invoice/{invoice}", name="app_user_invoice")
     */
    public function userInvoice(
        Invoice $invoice,
        CurrentUserService $currentUserService,
        SettingsService $settingsService,
        TranslatorInterface $translator
    ): void {
        $this->dennyUnlessUser();
        if ($invoice->getUser() !== $currentUserService->getUser()) {
            throw new UnauthorizedHttpException('Invoice does not belong to current user');
        }

        $settingsDto = $settingsService->getSettingsDtoWithoutCache();
        $logoPath = FilePath::getPublicDir() . \DIRECTORY_SEPARATOR . $settingsDto->getLogoPath();
        if (null === $settingsDto->getLogoPath() || !\file_exists($logoPath)) {
            $logoPath = FilePath::getPublicDir() . 'static/system/logo_default.png';
            FileHelper::throwExceptionIfPathOutsideDir($logoPath, FilePath::getStaticPath());
        } else {
            FileHelper::throwExceptionIfPathOutsideDir($logoPath, FilePath::getLogoPath());
        }
        FileHelper::throwExceptionIfNotImage($logoPath);

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', true);
        $domPdf = new Dompdf($pdfOptions);
        $html = $this->renderView('user/invoice/user_invoice.html.twig', [
            'invoice' => $invoice,
            'logoImageBase64' => \base64_encode(\file_get_contents($logoPath)),
        ]);
        $domPdf->loadHtml($html);
        $domPdf->setPaper('A4', 'portrait');
        $domPdf->render();

        $downloadedFileName = SlugHelper::getSlug($translator->trans('trans.invoice_file_prefix') . $invoice->getInvoiceNumber(), '_');
        $domPdf->stream(
            $downloadedFileName, [
            'Attachment' => true
        ]);
    }
}
