<?php

namespace App\Security\Base;

interface EnablableInterface
{
    public function getEnabled(): bool;
}
