<?php

declare(strict_types=1);

namespace App\Service\System\Maintenance\RegenerateListing\Dto;

class RegenerateListingDto
{
    /** @var int|null */
    private $limit;

    /** @var int|null */
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
