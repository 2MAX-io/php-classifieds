<?php

declare(strict_types=1);

namespace App\Service\System\Maintenance\MoveFilesToNewStructure\Dto;

class MoveFilesToNewLocationDto
{
    /** @var bool */
    private $performMove = false;

    /** @var int|null */
    private $limit;

    public function getPerformMove(): bool
    {
        return $this->performMove;
    }

    public function setPerformMove(bool $performMove): void
    {
        $this->performMove = $performMove;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }
}
