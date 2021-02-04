<?php

declare(strict_types=1);

namespace App\Service\System\Pagination;

use Pagerfanta\Pagerfanta;

class PaginationDto
{
    /**
     * @var iterable
     */
    private $results;

    /**
     * @var Pagerfanta
     */
    private $pager;

    public function __construct(iterable $results, Pagerfanta $pager)
    {
        $this->pager = $pager;
        $this->results = $results;
    }

    /**
     * @return iterable
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
