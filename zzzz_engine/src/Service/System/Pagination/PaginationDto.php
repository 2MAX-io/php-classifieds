<?php

declare(strict_types=1);

namespace App\Service\System\Pagination;

use Pagerfanta\Pagerfanta;

class PaginationDto
{
    /**
     * @var \Traversable
     */
    private $results;

    /**
     * @var Pagerfanta
     */
    private $pager;

    public function __construct(\Traversable $results, Pagerfanta $pager)
    {
        $this->pager = $pager;
        $this->results = $results;
    }

    /**
     * @return \Traversable
     */
    public function getResults(): \Traversable
    {
        return $this->results;
    }

    public function getPager(): Pagerfanta
    {
        return $this->pager;
    }
}
