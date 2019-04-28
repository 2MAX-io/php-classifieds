<?php

namespace App\Service\Listing\ListingList;

use App\Entity\Category;
use App\Entity\Listing;
use Pagerfanta\Pagerfanta;

class ListingListDto
{
    /**
     * @var \Traversable
     */
    private $results;

    /**
     * @var Pagerfanta
     */
    private $pager;

    /**
     * @var Category
     */
    private $category;

    /**
     * @var int
     */
    private $pageNumber;

    /**
     * @return \Traversable|Listing[]
     */
    public function getResults(): \Traversable
    {
        return $this->results;
    }

    public function getPager(): Pagerfanta
    {
        return $this->pager;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

    public function setPager(Pagerfanta $pager): void
    {
        $this->pager = $pager;
    }

    public function setResults(\Traversable $results): void
    {
        $this->results = $results;
    }

    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    public function setPageNumber(int $pageNumber): void
    {
        $this->pageNumber = $pageNumber;
    }
}
