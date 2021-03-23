<?php

/** @noinspection PhpInternalEntityUsedInspection */

declare(strict_types=1);

namespace App\Service\System\Email\Transport;

use Symfony\Component\Mailer\Transport\AbstractTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;
use Symfony\Component\Mailer\Transport\TransportInterface;

class SelfSignedEmailTransportFactory extends AbstractTransportFactory
{
    public function create(Dsn $dsn): TransportInterface
    {
        $tls = 'smtps' === $dsn->getScheme() ? true : null;
        $port = $dsn->getPort(0);
        $host = $dsn->getHost();

        $transport = new EsmtpTransport($host, $port, $tls, $this->dispatcher, $this->logger);

        // START - CUSTOM
        $stream = $transport->getStream();
        if ($stream instanceof SocketStream) {
            /**
             * @psalm-suppress InternalMethod
             */
            $stream->setStreamOptions([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ]);
        }
        // END - CUSTOM

        if ($user = $dsn->getUser()) {
            $transport->setUsername($user);
        }

        if ($password = $dsn->getPassword()) {
            $transport->setPassword($password);
        }

        return $transport;
    }

    /**
     * @return string[]
     */
    protected function getSupportedSchemes(): array
    {
        return ['selfsignedsmtp'];
    }
}
