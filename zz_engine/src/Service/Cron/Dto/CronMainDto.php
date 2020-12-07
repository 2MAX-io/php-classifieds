<?php

declare(strict_types=1);

namespace App\Service\Cron\Dto;

class CronMainDto
{
    /** @var bool */
    private $ignoreDelay = false;

    public function getIgnoreDelay(): bool
    {
        return $this->ignoreDelay;
    }

    public function setIgnoreDelay(bool $ignoreDelay): void
    {
        $this->ignoreDelay = $ignoreDelay;
    }
}
