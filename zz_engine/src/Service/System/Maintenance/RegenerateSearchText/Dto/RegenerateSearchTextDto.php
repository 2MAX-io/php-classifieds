<?php

declare(strict_types=1);

namespace App\Service\System\Maintenance\RegenerateSearchText\Dto;

class RegenerateSearchTextDto
{
    /** @var null|int */
    private $limit;

    /** @var null|int */
    private $timeLimitSeconds;

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }

    public function getTimeLimitSeconds(): ?int
    {
        return $this->timeLimitSeconds;
    }

    public function setTimeLimitSeconds(?int $timeLimitSeconds): void
    {
        $this->timeLimitSeconds = $timeLimitSeconds;
    }
}
