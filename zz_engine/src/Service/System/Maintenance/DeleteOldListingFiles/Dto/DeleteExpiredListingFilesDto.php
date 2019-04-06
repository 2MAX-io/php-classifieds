<?php

declare(strict_types=1);

namespace App\Service\System\Maintenance\DeleteOldListingFiles\Dto;

class DeleteExpiredListingFilesDto
{
    /** @var int */
    private $daysOldToDelete;

    /** @var bool */
    private $performFileDeletion = false;

    /** @var int|null */
    private $limit;

    public function getDaysOldToDelete(): int
    {
        return $this->daysOldToDelete;
    }

    public function setDaysOldToDelete(int $daysOldToDelete): void
    {
        $this->daysOldToDelete = $daysOldToDelete;
    }

    public function getPerformFileDeletion(): bool
    {
        return $this->performFileDeletion;
    }

    public function setPerformFileDeletion(bool $performFileDeletion): void
    {
        $this->performFileDeletion = $performFileDeletion;
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
