<?php

declare(strict_types=1);

namespace App\Service\System\Text;

use libphonenumber\PhoneNumberUtil;

class PhoneNumberService
{
    public static function factory(): PhoneNumberUtil
    {
        return PhoneNumberUtil::getInstance();
    }
}
