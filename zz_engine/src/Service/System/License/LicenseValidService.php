<?php

declare(strict_types=1);

namespace App\Service\System\License;

use App\Helper\JsonHelper;
use App\Service\System\Signature\VerifySignature;

class LicenseValidService
{
    public static function isLicenseValid(string $licenseText): bool
    {
        try {
            $licenseDecoded = JsonHelper::toArray(\base64_decode($licenseText));
        } catch (\Throwable $e) {
            return false;
        }

        $signatureCorrect = VerifySignature::authenticate(
            $licenseDecoded['payload'],
            $licenseDecoded['payloadSignature'],
        );

        return $signatureCorrect;
    }
}
