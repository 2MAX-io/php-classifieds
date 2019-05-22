<?php

declare(strict_types=1);

namespace App\Service\System\Upgrade;

class VerifyNormalSecurity
{
    public static function authenticate(string $payload, string $signature): bool
    {
        return 1 === \openssl_verify($payload, \base64_decode($signature), \trim(static::getPublicKey()), OPENSSL_ALGO_SHA256);
    }

    public static function getPublicKey(): string
    {
        return <<<'EOT'
-----BEGIN PUBLIC KEY-----
MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEA7gRqv4Zwnnix2y+h8I1S
RRUW03DW33z1OyRcU3JtV5TSmQnIfQH+y7NJRr3QyDZW4lDoYiRsW55R8Dwb5UEn
JCLx+SIQNAbMNQakyHkyvYhd8XV8oksyxrZePGkMdFJdARm4cWyZOsONX4ZNFa4/
jYpFRfLk4UmE6Kg6BZetY19lWjyjZT7W3PFfhF6JIOTdDtow7eFi7Z9fw3jKpb0x
MBeJP4v/MbvtKa0UJlVDvT7ho8juOoXI4A3arBjkov55rHVfeE1aLAu2ARSG8AOx
Xz7QaqiG5EIBwumsn+4m/EXSG5RCDXkqwDa+enFVqMoqHkLOCybjb30dE11TQ6cf
gNZrSDXPkVqmwQju0FtccSlVtZUZbDowHAe1ZqXIB47YtXT0MeGT6gS1RQ+L3VK0
B5WfXH8O860Gj78yHFoCn1WYuxZfcPmpYNP6uUOchl3Pijgae3G5+ldfYYStZwND
pzdutAYRrjp82da1mF95ufKr2+DYeIjN4ofbOfUO77MC+QsFJFCcUZLoHcsNVT0d
8WOQLCQYMMO9Sqa2ocx3FCqqA6N7EoAm3hpLczBjZ59M67hJdeMtWB3WNqsOy5h5
G4I5hmtKdLfSA96q0fKAkrUGJS9ScNqyETG2Kd/O/q8qVN+O0qjZ65DuWBN6svra
ntDo/XOZQiq7MlUmEByEYLkCAwEAAQ==
-----END PUBLIC KEY-----
EOT;
    }
}
