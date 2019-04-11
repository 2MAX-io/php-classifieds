<?php

declare(strict_types=1);

namespace App\Service\Log;

use App\Entity\Listing;
use App\Entity\Log;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class LogIpService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function saveLog(Listing $listing)
    {
        $log = new Log();
        $log->setSourceIp($_SERVER['REMOTE_ADDR']);
        $log->setDestinationIp($_SERVER['SERVER_ADDR']);
        $log->setDatetime(\DateTime::createFromFormat('U', (string) $_SERVER['REQUEST_TIME']));
        $log->setListingId($listing->getId());

        $requestTimeString = DateTime::createFromFormat('U.u', (string) $_SERVER['REQUEST_TIME_FLOAT'])->format('Y-m-d H:i:s.u P');
        $currentServerTime = DateTime::createFromFormat('U.u', (string) microtime(true))->format('Y-m-d H:i:s.u P');

        $logText = <<<END
Connection:
{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} --> {$_SERVER['SERVER_ADDR']}:{$_SERVER['SERVER_PORT']}

Request time: {$requestTimeString}
Unix request time float: {$_SERVER['REQUEST_TIME_FLOAT']}
Current server time: {$currentServerTime}


END;

        $logText .= $this->getServerAsString();

        $log->setText($logText);

        $this->em->persist($log);
    }

    private function getServerAsString(): string
    {
        $otherInfo = [
            'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? '',
            'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? '',
            'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'SERVER_ADDR' => $_SERVER['SERVER_ADDR'],
            'SERVER_PORT' => $_SERVER['SERVER_PORT'],
            'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'],
            'REMOTE_PORT' => $_SERVER['REMOTE_PORT'],
            'REMOTE_HOST' => $_SERVER['REMOTE_HOST'] ?? '',
            'HTTP_CF_CONNECTING_IP' => $_SERVER['HTTP_CF_CONNECTING_IP'] ?? '',
            'HTTP_ORIGIN' => $_SERVER['HTTP_ORIGIN'] ?? '',
            'SERVER_NAME' => $_SERVER['SERVER_NAME'] ?? '',
            'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? '',
            'SERVER_PROTOCOL' => $_SERVER['SERVER_PROTOCOL'] ?? '',
            'HTTP_REFERER' => $_SERVER['HTTP_REFERER'] ?? '',
            'REQUEST_TIME_FLOAT' => $_SERVER['REQUEST_TIME_FLOAT'],
        ];

        $return = '';
        foreach ($otherInfo as $name => $value) {
            $return .= "$name => {$value} \r\n";
        }

        return $return;
    }
}
