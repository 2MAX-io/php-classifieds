<?php

declare(strict_types=1);

namespace App\Service\System\SystemLog\PoliceLog;

use App\Helper\DateHelper;
use App\Service\System\SystemLog\PoliceLog\Dto\PoliceLogDataDto;
use Psr\Log\LoggerInterface;

class PoliceLogHelperService
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getPoliceLogData(): PoliceLogDataDto
    {
        $realIpBehindProxy = '';
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $realIpBehindProxy = $_SERVER['HTTP_X_FORWARDED_FOR'].' --> ';
        }
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $realIpBehindProxy = $_SERVER['HTTP_CF_CONNECTING_IP'].' --> '.$_SERVER['HTTP_CF_VISITOR'] ?? ''.' --> ';
        }

        $serverRequestTime = (string) $_SERVER['REQUEST_TIME'];
        $datetimeFromRequestTime = \DateTime::createFromFormat('U', $serverRequestTime);
        if (false === $datetimeFromRequestTime) {
            $this->logger->error('can not parse _SERVER REQUEST_TIME: `{requestTime}`', [
                'requestTime' => $serverRequestTime,
            ]);

            $datetimeFromRequestTime = DateHelper::create();
        }

        $log = new PoliceLogDataDto();
        $log->setSourceIp($_SERVER['REMOTE_ADDR']);
        $log->setDestinationIp($_SERVER['SERVER_ADDR']);
        $log->setSourcePort($_SERVER['REMOTE_PORT']);
        $log->setDestinationPort($_SERVER['SERVER_PORT']);
        $log->setRealSourceIpBehindProxy($realIpBehindProxy);
        $log->setDatetime($datetimeFromRequestTime);
        $log->setRequestTimeFloat($_SERVER['REQUEST_TIME_FLOAT']);
        $log->setRequestTimeString(DateHelper::fromMicroTimeFloat($_SERVER['REQUEST_TIME_FLOAT'])->format('Y-m-d H:i:s.u P'));
        $log->setCurrentServerTime(DateHelper::fromMicroTimeFloat(\microtime(true))->format('Y-m-d H:i:s.u P'));

        $connectionDataText = <<<END
Connection:
{$log->getRealSourceIpBehindProxy()}{$log->getSourceIp()}:{$log->getSourcePort()} --> {$log->getDestinationIp()}:{$log->getDestinationPort()}

Request time: {$log->getRequestTimeString()}
Request time float (unix time): {$log->getRequestTimeFloat()}
Server time when saving this log: {$log->getCurrentServerTime()}
END;
        $log->setConnectionDataText($connectionDataText);

        return $log;
    }

    public function getShortLogString(): string
    {
        $log = $this->getPoliceLogData();
        $logText = <<<END
{$log->getRequestTimeString()}: {$log->getRealSourceIpBehindProxy()}{$log->getSourceIp()}:{$log->getSourcePort()} --> {$log->getDestinationIp()}:{$log->getDestinationPort()}
END;

        return $logText;
    }
}
