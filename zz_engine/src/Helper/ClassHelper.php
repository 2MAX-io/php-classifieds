<?php

declare(strict_types=1);

namespace App\Helper;

class ClassHelper
{
    public static function getPropertyNameFromMethodName(string $methodName): string
    {
        $propertyName = $methodName;
        $propertyName = \preg_replace('~^get(.+)~', '$1', $propertyName);

        return \lcfirst($propertyName);
    }
}
