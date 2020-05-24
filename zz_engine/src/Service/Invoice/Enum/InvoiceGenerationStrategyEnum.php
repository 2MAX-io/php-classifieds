<?php

declare(strict_types=1);

namespace App\Service\Invoice\Enum;

class InvoiceGenerationStrategyEnum
{
    public const AUTO = 'auto';
    public const MANUAL = 'manual';
    public const EXTERNAL_SYSTEM = 'external_system';
    public const INFAKT_PL = 'infakt_pl';
}
