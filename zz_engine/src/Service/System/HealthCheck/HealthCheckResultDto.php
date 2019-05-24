<?php

declare(strict_types=1);

namespace App\Service\System\HealthCheck;

class HealthCheckResultDto
{
    /**
     * @var string|null
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

    public function getMessage(): string
    {
        return $this->message;
    }

    public function isProblem(): bool
    {
        return $this->problem;
    }
}
