<?php

declare(strict_types=1);

namespace App\Service\Maintenance\MoveFilesToNewStructure\Dto;

class MoveFilesToNewLocationDto
{
    /** @var boolean */
    private $performMove = false;

    /** @var null|int */
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
