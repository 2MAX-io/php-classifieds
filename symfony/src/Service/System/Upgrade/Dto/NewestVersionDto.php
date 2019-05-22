<?php

declare(strict_types=1);

namespace App\Service\System\Upgrade\Dto;

class NewestVersionDto
{
    /**
     * @var int
     */
    private $version;

    /**
     * @var string
     */
    private $date;

    public function __construct(int $version, string $date)
    {
        $this->version = $version;
        $this->date = $date;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getDate(): string
    {
        return $this->date;
    }
}
