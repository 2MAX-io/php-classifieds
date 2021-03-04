<?php

declare(strict_types=1);

namespace App\Service\System\HealthCheck;

class HealthCheckResultDto
{
    /**
     * @var null|string
     */
    private $message;

    /**
     * @var bool
     */
    private $problem;

    public function __construct(bool $problem, string $message = null)
    {
        $this->message = $message;
        $this->problem = $problem;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getMessageNotNull(): string
    {
        if (null === $this->message) {
            throw new \RuntimeException('message is null');
        }

        return $this->message;
    }

    public function hasProblem(): bool
    {
        return $this->problem;
    }
}
