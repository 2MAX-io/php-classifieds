<?php

declare(strict_types=1);

namespace App\Service\System\Pagination\Dto;

use Pagerfanta\Pagerfanta;

class PaginationDto
{
    /**
     * @var iterable|mixed[]
     */
    private $results;

    /**
     * @var Pagerfanta
     */
    private $pager;

    /**
     * @param iterable|mixed[] $results
     */
    public function __construct(iterable $results, Pagerfanta $pager)
    {
        $this->pager = $pager;
        $this->results = $results;
    }

    /**
     * @return iterable|mixed[]
     */
    public function getResults(): iterable
    {
        return $this->results;
    }

    public function getPager(): Pagerfanta
    {
        return $this->pager;
    }
}
