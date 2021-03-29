<?php

declare(strict_types=1);

namespace App\Service\User\Invoice;

use App\Entity\Invoice;
use App\Helper\FileHelper;
use App\Helper\FilePath;
use App\Service\Setting\SettingsService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

class UserInvoiceSingleService
{
    /**
     * @var SettingsService
     */
    private $settingsService;

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(SettingsService $settingsService, Environment $twig)
    {
        $this->settingsService = $settingsService;
        $this->twig = $twig;
    }

    public function renderInvoicePdf(Invoice $invoice): Dompdf
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', true);
        $domPdf = new Dompdf($pdfOptions);
        $html = $this->twig->render('user/invoice/user_invoice.html.twig', [
            'invoice' => $invoice,
            'logoImageBase64' => \base64_encode(\file_get_contents($this->getLogoPath()) ?: ''),
        ]);
        $domPdf->loadHtml($html);
        $domPdf->setPaper('A4', 'portrait');
        $domPdf->render();

        return $domPdf;
    }

    private function getLogoPath(): string
    {
        $settingsDto = $this->settingsService->getSettingsDtoWithoutCache();
        $logoPath = FilePath::getPublicDir().\DIRECTORY_SEPARATOR.$settingsDto->getLogoPath();
        if (null === $settingsDto->getLogoPath() || !\file_exists($logoPath)) {
            $logoPath = FilePath::getPublicDir().'/static/system/logo_default.png';
            FileHelper::throwExceptionIfPathOutsideDir($logoPath, FilePath::getStaticPath());
        } else {
            FileHelper::throwExceptionIfPathOutsideDir($logoPath, FilePath::getLogoPath());
        }
        FileHelper::throwExceptionIfNotImage($logoPath);
        FileHelper::throwExceptionIfUnsafeFilename($logoPath);

        return $logoPath;
    }
}
