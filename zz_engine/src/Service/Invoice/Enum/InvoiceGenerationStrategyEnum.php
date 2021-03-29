<?php

declare(strict_types=1);

namespace App\Service\Invoice\Enum;

class InvoiceGenerationStrategyEnum
{
    public const DISABLED = 'disabled';
    public const AUTO = 'auto';
    public const EXTERNAL_SYSTEM = 'external_system';
}
