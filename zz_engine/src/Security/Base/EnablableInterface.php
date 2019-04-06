<?php

declare(strict_types=1);

namespace App\Security\Base;

interface EnablableInterface
{
    public function getEnabled(): bool;
}
