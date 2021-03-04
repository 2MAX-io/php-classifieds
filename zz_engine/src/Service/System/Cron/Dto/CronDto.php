<?php

declare(strict_types=1);

namespace App\Service\System\Cron\Dto;

class CronDto
{
    /** @var bool */
    private $ignoreDelay = false;

    /** @var bool */
    private $night = false;

    public function getIgnoreDelay(): bool
    {
        return $this->ignoreDelay;
    }

    public function setIgnoreDelay(bool $ignoreDelay): void
    {
        $this->ignoreDelay = $ignoreDelay;
    }

    public function isNight(): bool
    {
        return $this->night;
    }

    public function setNight(bool $night): void
    {
        $this->night = $night;
    }
}
