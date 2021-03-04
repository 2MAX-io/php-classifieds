<?php

declare(strict_types=1);

namespace App\Service\System\SystemLog\PoliceLog\Dto;

class PoliceLogDataDto
{
    /**
     * @var \DateTimeInterface
     */
    private $datetime;

    /**
     * @var string
     */
    private $sourceIp;

    /**
     * @var string
     */
    private $destinationIp;

    /**
     * @var string
     */
    private $sourcePort;

    /**
     * @var string
     */
    private $destinationPort;

    /**
     * @var string
     */
    private $realSourceIpBehindProxy;

    /**
     * @var float
     */
    private $requestTimeFloat;

    /**
     * @var string
     */
    private $requestTimeString;

    /**
     * @var string
     */
    private $currentServerTime;

    /**
     * @var string
     */
    private $connectionDataText;

    public function getDatetime(): \DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): void
    {
        $this->datetime = $datetime;
    }

    public function getSourceIp(): string
    {
        return $this->sourceIp;
    }

    public function setSourceIp(string $sourceIp): void
    {
        $this->sourceIp = $sourceIp;
    }

    public function getDestinationIp(): string
    {
        return $this->destinationIp;
    }

    public function setDestinationIp(string $destinationIp): void
    {
        $this->destinationIp = $destinationIp;
    }

    public function getRequestTimeString(): string
    {
        return $this->requestTimeString;
    }

    public function setRequestTimeString(string $requestTimeString): void
    {
        $this->requestTimeString = $requestTimeString;
    }

    public function getCurrentServerTime(): string
    {
        return $this->currentServerTime;
    }

    public function setCurrentServerTime(string $currentServerTime): void
    {
        $this->currentServerTime = $currentServerTime;
    }

    public function getRequestTimeFloat(): float
    {
        return $this->requestTimeFloat;
    }

    public function setRequestTimeFloat(float $requestTimeFloat): void
    {
        $this->requestTimeFloat = $requestTimeFloat;
    }

    public function getRealSourceIpBehindProxy(): string
    {
        return $this->realSourceIpBehindProxy;
    }

    public function setRealSourceIpBehindProxy(string $realSourceIpBehindProxy): void
    {
        $this->realSourceIpBehindProxy = $realSourceIpBehindProxy;
    }

    public function getConnectionDataText(): string
    {
        return $this->connectionDataText;
    }

    public function setConnectionDataText(string $connectionDataText): void
    {
        $this->connectionDataText = $connectionDataText;
    }

    public function getSourcePort(): string
    {
        return $this->sourcePort;
    }

    public function setSourcePort(string $sourcePort): void
    {
        $this->sourcePort = $sourcePort;
    }

    public function getDestinationPort(): string
    {
        return $this->destinationPort;
    }

    public function setDestinationPort(string $destinationPort): void
    {
        $this->destinationPort = $destinationPort;
    }
}
