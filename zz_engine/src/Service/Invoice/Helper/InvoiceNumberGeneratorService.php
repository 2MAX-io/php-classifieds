<?php

declare(strict_types=1);

namespace App\Service\Invoice\Helper;

use App\Entity\Invoice;
use App\Service\Setting\SettingsService;

class InvoiceNumberGeneratorService
{
    /**
     * @var SettingsService
     */
    private $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    public function getNextInvoiceNumber(Invoice $invoice): string
    {
        $settingsDto = $this->settingsService->getSettingsDtoWithoutCache();

        if ($settingsDto->getInvoiceNumberPrefix()) {
            return $settingsDto->getInvoiceNumberPrefix().$invoice->getId();
        }

        return (string) $invoice->getId();
    }
}
