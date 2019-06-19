<?php

declare(strict_types=1);

namespace App\Helper;

/**
 * contains method that might be useful latter, but not sure,
 * refactor if not used more than one time
 */
class Helper
{
    public static function getPropertyNameFromMethodName(string $methodName): string
    {
        $return = $methodName;
        $return = \preg_replace('~^get(.+)~', '$1', $return);
        $return = \lcfirst($return);

        return $return;
    }
}
