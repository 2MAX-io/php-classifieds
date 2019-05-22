<?php

declare(strict_types=1);

namespace App\Service\System\Upgrade;

class VerifyHighSecurity
{
    public static function authenticate(string $payload, string $signature): bool
    {
        return 1 === \openssl_verify($payload, \base64_decode($signature), \trim(static::getPublicKey()), OPENSSL_ALGO_SHA256);
    }

    public static function getPublicKey(): string
    {
        return <<<'EOT'
-----BEGIN PUBLIC KEY-----
MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAw5HFK+TGPGfbYJ9RK3Pk
YJarNT/3WkT/cm8PV2H3WwkrIko9TNbjmuvaaiB38jWamOGhPLfGTgraXxaJFZDb
7CI7BPLOacG5FdQ06HIMZ0yadxroaa67VfBp0ycri10BYd9hHGM6imaLcQ7BCbaQ
+oz3DiFiDosPyFI25+2dNPrD9bH9YpCO31iOrckRPk6HSSLe49ryDBuYjdPxnRIz
IL2QnVFuYWY9qikycQOGiTDiqqVZvNBZ/+rLtie/S3K8rBwrksbMEoyLbLJdr4wu
TufGnuoUdGVZHoCSGQX2PjycqdqE0BJVY4uqSeFOcHRKHwTbZ080qqHsHZdw9YGo
UOl99KT15SdPzrzG2Z0ApUBcTcNrpWvBYCbgrBvLnKPKkCyspPO5sksE7JYZoh+y
HmtZ/hG+i45/0zkyDz0tsEmNhCY4INN2Xhwk6ZkqGan4D6i+v7YfEBkSrR01Bwmp
BJKqe8kXtWrwsQFGvrI9yY1hIj/mhVz9+PGnEUc4RiwEWh9t9dGP59owHRWLB88V
f4h27OBFKHGa0M/Y/9OZ5v9+kqYkMsjV6gR9BmMFZ5Wp5KrEbqvEd5W3yxAXBLAe
pTfFwtF1DMEDS1m/GvzEsHrV4IOAIR7IBCXQX6EkXGQWNK1BQli3VhTP3axhajK3
ikKQMirigSIQzu1fSTj0JdUCAwEAAQ==
-----END PUBLIC KEY-----
EOT;
    }
}
