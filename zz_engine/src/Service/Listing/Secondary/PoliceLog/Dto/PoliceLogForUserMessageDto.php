<?php

declare(strict_types=1);

namespace App\Service\Listing\Secondary\PoliceLog\Dto;

class PoliceLogForUserMessageDto
{
    /** @var null|int */
    private $userId;

    /** @var null|int */
    private $listingId;

    /** @var null|int */
    private $threadId;

    /** @var null|string */
    private $query;

    /** @var null|int */
    private $page;

    /** @var string[] */
    private $where = [];

    /** @var array<string,mixed> */
    private $sqlParameters = [];

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): void
    {
        $this->userId = $userId;
    }

    public function getListingId(): ?int
    {
        return $this->listingId;
    }

    public function setListingId(?int $listingId): void
    {
        $this->listingId = $listingId;
    }

    public function getThreadId(): ?int
    {
        return $this->threadId;
    }

    public function setThreadId(?int $threadId): void
    {
        $this->threadId = $threadId;
    }

    public function getQuery(): ?string
    {
        return $this->query;
    }

    public function setQuery(?string $query): void
    {
        $this->query = $query;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function setPage(?int $page): void
    {
        $this->page = $page;
    }

    /**
     * @return string[]
     */
    public function getWhere(): array
    {
        return $this->where;
    }

    /**
     * @param string[] $where
     */
    public function setWhere(array $where): void
    {
        $this->where = $where;
    }

    /**
     * @return array<string,mixed>
     */
    public function getSqlParameters(): array
    {
        return $this->sqlParameters;
    }

    /**
     * @param array<string,mixed> $sqlParameters
     */
    public function setSqlParameters(array $sqlParameters): void
    {
        $this->sqlParameters = $sqlParameters;
    }
}
