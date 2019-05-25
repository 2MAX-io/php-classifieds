<?php

declare(strict_types=1);

namespace App\Service\Listing\ListingList;

use App\Entity\Category;
use App\Entity\CustomField;
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
     * @var boolean
     */
    private $lastAddedListFlag = false;

    /**
     * @var string
     */
    private $route;

    /**
     * @var int|null
     */
    private $redirectToPageNumber;

    /**
     * @var CustomField[]
     */
    private $customFieldForCategoryList;

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

    public function isLastAddedListFlag(): bool
    {
        return $this->lastAddedListFlag;
    }

    public function setLastAddedListFlag(bool $lastAddedListFlag): void
    {
        $this->lastAddedListFlag = $lastAddedListFlag;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function setRoute(string $route): void
    {
        $this->route = $route;
    }

    public function getRedirectToPageNumber(): ?int
    {
        return $this->redirectToPageNumber;
    }

    public function setRedirectToPageNumber(?int $redirectToPageNumber): void
    {
        $this->redirectToPageNumber = $redirectToPageNumber;
    }

    public function getCustomFieldForCategoryList(): array
    {
        return $this->customFieldForCategoryList;
    }

    public function setCustomFieldForCategoryList(array $customFieldForCategoryList): void
    {
        $this->customFieldForCategoryList = $customFieldForCategoryList;
    }
}