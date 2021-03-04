<?php

declare(strict_types=1);

namespace App\Service\Listing\ListingList\Dto;

use App\Entity\Category;
use App\Entity\CustomField;
use App\Entity\Listing;
use App\Entity\User;
use Pagerfanta\Pagerfanta;

class ListingListDto
{
    /**
     * @var iterable|Listing[]
     */
    private $results;

    /**
     * @var string
     */
    private $route;

    /**
     * @var int
     */
    private $pageNumber;

    /**
     * @var Pagerfanta
     */
    private $pager;

    /**
     * @var Category
     */
    private $category;

    /**
     * @var bool
     */
    private $lastAddedList = false;

    /**
     * @var null|int
     */
    private $redirectToPageNumber;

    /**
     * @var CustomField[]
     */
    private $categoryCustomFields;

    /**
     * @var null|User
     */
    private $filterByUser;

    /**
     * @var null|string
     */
    private $categorySlug;

    /**
     * @var null|string
     */
    private $redirectToRoute;

    /**
     * @var null|string
     */
    private $minPrice;

    /**
     * @var null|string
     */
    private $maxPrice;

    /**
     * @var null|string
     */
    private $searchQuery;

    /**
     * @var array<int, array>
     */
    private $filterByCustomFields = [];

    /**
     * @return iterable|Listing[]
     */
    public function getResults(): iterable
    {
        return $this->results;
    }

    public function getPager(): Pagerfanta
    {
        return $this->pager;
    }

    public function setPager(Pagerfanta $pager): void
    {
        $this->pager = $pager;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function getCategoryNotNull(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

    /**
     * @param iterable|Listing[] $results
     */
    public function setResults(iterable $results): void
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

    public function isLastAddedList(): bool
    {
        return $this->lastAddedList;
    }

    public function setLastAddedList(bool $lastAddedList): void
    {
        $this->lastAddedList = $lastAddedList;
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

    /**
     * @return CustomField[]
     */
    public function getCategoryCustomFields(): array
    {
        return $this->categoryCustomFields;
    }

    /**
     * @param CustomField[] $categoryCustomFields
     */
    public function setCategoryCustomFields(array $categoryCustomFields): void
    {
        $this->categoryCustomFields = $categoryCustomFields;
    }

    public function getFilterByUser(): ?User
    {
        return $this->filterByUser;
    }

    public function setFilterByUser(?User $filterByUser): void
    {
        $this->filterByUser = $filterByUser;
    }

    public function getCategorySlug(): ?string
    {
        return $this->categorySlug;
    }

    public function setCategorySlug(?string $categorySlug): void
    {
        $this->categorySlug = $categorySlug;
    }

    public function getRedirectToRoute(): ?string
    {
        return $this->redirectToRoute;
    }

    public function setRedirectToRoute(?string $redirectToRoute): void
    {
        $this->redirectToRoute = $redirectToRoute;
    }

    public function getMinPrice(): ?string
    {
        return $this->minPrice;
    }

    public function setMinPrice(?string $minPrice): void
    {
        $this->minPrice = $minPrice;
    }

    public function getMaxPrice(): ?string
    {
        return $this->maxPrice;
    }

    public function setMaxPrice(?string $maxPrice): void
    {
        $this->maxPrice = $maxPrice;
    }

    public function getSearchQuery(): ?string
    {
        return $this->searchQuery;
    }

    public function setSearchQuery(?string $searchQuery): void
    {
        $this->searchQuery = $searchQuery;
    }

    /**
     * @return array<int, array>
     */
    public function getFilterByCustomFields(): array
    {
        return $this->filterByCustomFields;
    }

    /**
     * @param array<int, array> $filterByCustomFields
     */
    public function setFilterByCustomFields(array $filterByCustomFields): void
    {
        $this->filterByCustomFields = $filterByCustomFields;
    }
}
