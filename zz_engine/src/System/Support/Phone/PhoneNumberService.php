<?php

declare(strict_types=1);

namespace App\System\Support\Phone;

use libphonenumber\PhoneNumberUtil;

class PhoneNumberService
{
    public static function factory(): PhoneNumberUtil
    {
        return PhoneNumberUtil::getInstance();
    }
}
