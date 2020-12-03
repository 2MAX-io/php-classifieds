<?php

declare(strict_types=1);

namespace App\Service\Cron\DeleteOldListingFiles\Dto;

class DeleteOldListingFilesDto
{
    /** @var int */
    private $deleteOlderThanInDays;

    /** @var boolean */
    private $performFileDeletion = false;

    /** @var null|int */
    private $limit;

    public function getDeleteOlderThanInDays(): int
    {
        return $this->deleteOlderThanInDays;
    }

    public function setDeleteOlderThanInDays(int $deleteOlderThanInDays): void
    {
        $this->deleteOlderThanInDays = $deleteOlderThanInDays;
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
